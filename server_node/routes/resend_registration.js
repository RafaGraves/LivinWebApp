const express = require('express');
const router = express.Router();
const {performQuery} = require('../public/javascripts/db_query');

const fs = require('fs');
const path = require('path');
const nodemailer = require("nodemailer");

const mailerTransporter = nodemailer.createTransport({
    service: process.env.MAILER_SERVICE,
    auth: {
        user: process.env.MAILER_ADDRESS,
        pass: process.env.MAILER_PASSWORD
    }
});


router.post('/:new_url', async function (req, res, next) {
    console.log(`resend requested for ID: ${req.params.new_url}`);

    // Update the time
    const updateQuery = {
        name: 'updateQuery',
        text: 'UPDATE public.signup_mail SET timestamp=DEFAULT WHERE url = $1;',
        values: [req.params.new_url]
    };

    try {
        // Update the time
        await performQuery(updateQuery);

        // This query will give us the name and the last name from the URL passed to the backend as well we have to retrieve the email
        const selectQuery = {
            name: 'selectQuery',
            text: 'SELECT sp.usr_id, usr.firstname AS fn, usr.lastname AS ln, usr.email AS ml FROM public.signup_mail AS sp JOIN public."user" AS usr ON usr.id = sp.usr_id WHERE sp.url = $1',
            values: [req.params.new_url]
        }

        const results = await performQuery(selectQuery);

        if (results.rowCount === 0) {
            throw Error("User data is not available");
        }

        // Load the template file
        let mailTemplateContents = fs.readFileSync(path.resolve('server/templates/registration_email.html'), {encoding: 'utf8'});

        const newConfirmationLink = process.env.THIS_HOST + '/confirmation/' + req.params.new_url;

        mailTemplateContents = mailTemplateContents.replace('##NAME', results.rows[0].fn);
        mailTemplateContents = mailTemplateContents.replace('##LASTNAME', results.rows[0].ln);
        mailTemplateContents = mailTemplateContents.replace('##LINK', newConfirmationLink);

        let mailOptions = {
            from: {
                name: 'Registro de Livin',
                address: process.env.MAILER_ADDRESS,
            },
            to: results.rows[0].ml,
            subject: 'Confirma tu registro con Livin',
            html: mailTemplateContents
        };

        await mailerTransporter.sendMail(mailOptions, function (error, info) {
            if (error) {
                console.error(error.message);
                return res.render('operation_error', {url: process.env.FRONTEND_URL});
            } else {
                console.log(`Email re-sent to ${ results.rows[0].ml}:${info.response}`);
            }
        });

        const redirectURL = process.env.FRONTEND_URL + '/signup/resend.html';
        return res.redirect(redirectURL);

    } catch (e) {
        console.error(e.message);
        return res.render('operation_error', {url: process.env.FRONTEND_URL});
    }


})

module.exports = router;