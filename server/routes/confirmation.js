const express = require('express');
const router = express.Router();

const {performQuery} = require('../public/javascripts/db_query');
const {JSONError} = require('../public/javascripts/cje');

const maxConfigTime = parseInt(process.env.USR_CONFIG_MAX_TIME, 10) || 30;

router.get('/:id_config', async function (req, res, next) {
        console.log(`Confirmation requested for ID: ${req.params.id_config}`);

        res.setHeader('Content-Type', 'application/json');

        try {
            // This query returns the time difference in minutes
            const query = {
                name: 'signupConfQuery',
                text: 'SELECT sp.usr_id,EXTRACT(EPOCH FROM (NOW() - sp.timestamp)) / 60 AS diff FROM public.signup_mail AS sp WHERE sp.url = $1',
                values: [req.params.id_config]
            }

            const result = await performQuery(query);

            if (result.rowCount === 0) {
                res.end(JSONError(2000, 'The confirmation URL does not exists'));
            } else {
                const diff = result.rows[0].diff;
                if (diff > maxConfigTime) {
                    res.end(JSONError(2001, 'The confirmation URL has expired'));
                } else {
                    const updateVerifyQuery = {
                        name: 'updateVerifyQuery',
                        text: 'UPDATE public.user AS sp SET verified=1 WHERE id = $1;',
                        values: [result.rows[0].usr_id]
                    }

                    await performQuery(updateVerifyQuery);

                    const verifyDeleteQuery = {
                        name: 'verifyQuery',
                        text: 'DELETE FROM public.signup_mail AS sp WHERE sp.url = $1;',
                        values: [req.params.id_config]
                    }

                    await performQuery(verifyDeleteQuery);

                    res.end(JSON.stringify(
                        {
                            code: 0,
                            message: 'e-mail confirmation was successfully done'
                        }
                    ));
                }
            }
        } catch (error) {
            res.end(JSONError(-2000, error.message));
        }
    }
)

module.exports = router;


