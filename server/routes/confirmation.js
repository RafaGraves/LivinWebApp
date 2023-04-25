const express = require('express');
const router = express.Router();

const {performQuery} = require('../public/javascripts/db_query')
const {JSONError} = require("../public/javascripts/cje");

const maxConfigTime = parseInt(process.env.USR_CONFIG_MAX_TIME, 10) || 30;

router.get('/:id_config', async function (req, res, next) {
        console.log(`Confirmation requested for ID: ${req.params.id_config}`);

        // This query returns the time difference in minutes
        try {
            const query = {
                name: 'signupConfQuery',
                text: 'SELECT sp.usr_id,EXTRACT(EPOCH FROM (NOW() - sp.timestamp)) / 60 AS diff FROM test_db_living.public.signup_mail AS sp WHERE sp.url = $1',
                values: [req.params.id_config]
            }

            const result = await performQuery(query);

            if (result.rowCount === 0) {
                res.setHeader('Content-Type', 'application/json');
                res.end(JSONError(2000, 'Confirmation URL does not exists'));
            } else {
                const diff = result.rows[0].diff;

                if( diff > maxConfigTime)
                {}
            }
        } catch (error) {
            console.error(error.message);
        }
    }
)

module.exports = router;


