const express = require('express');
const router = express.Router();
const crypto = require('crypto');
const {performQuery} = require('../public/javascripts/db_query');
const {JSONError} = require('../public/javascripts/cje');

const maxConfigTime = parseInt(process.env.USR_CONFIG_MAX_TIME, 10) || 30;

async function generateHashFromString(str) {
    return crypto.createHash('sha256').update(str).digest('hex');
}


router.get('/:id_config', async function (req, res, next) {
    console.log(`Confirmation requested for ID: ${req.params.id_config}`);

    try {
        // This query returns the time difference in minutes
        const query = {
            name: 'signupConfQuery',
            text: 'SELECT sp.usr_id,EXTRACT(EPOCH FROM (NOW() - sp.timestamp)) / 60 AS diff, usr.firstname AS fn, usr.lastname AS ln FROM public.signup_mail AS sp JOIN public."user" AS usr ON usr.id = sp.usr_id WHERE sp.url = $1',
            values: [req.params.id_config]
        }
        const result = await performQuery(query);

        if (result.rowCount === 0) {
            console.log('Confirmation link does not exists');
            const missingData = {
                url: process.env.FRONTEND_URL
            };
            return res.render('registration_missing', missingData);
        } else {
            const diff = result.rows[0].diff;
            if (diff > maxConfigTime) {
                // GENERATE THE NEW LINK IN THE SIGNUP TABLE
                const newURLLink = await generateHashFromString(req.params.id_config + new Date().toISOString());
                const signupDeleteQuery = {
                    name: 'verifyQuery',
                    text: 'UPDATE public.signup_mail SET url=$2 WHERE url = $1;',
                    values: [req.params.id_config, newURLLink]
                }
                await performQuery(signupDeleteQuery);

                // In this case we must
                const resendData = {
                    resend: process.env.THIS_HOST + '/signup/resend/' + newURLLink
                };

                return res.render('registration_expired', resendData);
            } else {
                const updateVerifyQuery = {
                    name: 'updateVerifyQuery',
                    text: 'UPDATE public.user AS sp SET verified=1 WHERE id = $1;',
                    values: [result.rows[0].usr_id]
                }

                await performQuery(updateVerifyQuery);

                const signupDeleteQuery = {
                    name: 'signupDeleteQuery',
                    text: 'DELETE FROM public.signup_mail AS sp WHERE sp.url = $1;',
                    values: [req.params.id_config]
                }
                await performQuery(signupDeleteQuery);

                return res.render('registration_conf', {
                    name: result.rows[0].fn,
                    lastName: result.rows[0].ln,
                    url: process.env.FRONTEND_URL
                });
            }
        }
    } catch (error) {
        console.error(error.message);
        return res.render('operation_error', {url: process.env.FRONTEND_URL});
    }
})

module.exports = router;


