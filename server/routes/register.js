const express = require('express');
const router = express.Router();
const {performQuery} = require('../public/javascripts/db_query');
const {JSONError} = require('../public/javascripts/cje');
const crypto = require('crypto');
const nodemailer = require('nodemailer');

const ajv = require('ajv').default;
const regValidationSchemaData = require('../schema/registration.schema.json');
const ajvValidator = new ajv();
const regValidationSchema = ajvValidator.compile(regValidationSchemaData);

const fs = require('fs');
const path = require('path');

async function generateHashFromString(str) {
    return crypto.createHash('sha256').update(str).digest('hex');
}

const mailerTransporter = nodemailer.createTransport({
    service: process.env.MAILER_SERVICE,
    auth: {
        user: process.env.MAILER_ADDRESS,
        pass: process.env.MAILER_PASSWORD
    }
});


router.post('/', async function (req, res, next) {
    const regData = req.body;

    // No matter what this endpoint will return JSON data
    res.setHeader('Content-Type', 'application/json');

    if (!regValidationSchema(regData)) {
        return res.status(400).json(JSONError(3000, 'Request data has the wrong format'));
    }

    const requestEmail = regData.email;

    // The user ID will be the first 16-bytes of the hashed email
    const userId = await generateHashFromString(requestEmail);


    try {

        // Check if the mail exists on the db
        const checkUsrExistsQuery = {
            name: 'checkUsrExistsQuery',
            text: 'SELECT * FROM public.user WHERE id=$1;',
            values: [userId]
        };

        const existResult = await performQuery(checkUsrExistsQuery);

        if (existResult.rowCount !== 0) {
            return res.status(400).json(JSONError(3001, 'e-mail already registered'));
        }

        // Add the user to the db
        const insertUserQuery = {
            name: 'insertUserQuery',
            text: 'INSERT INTO public.user (id, firstname, lastname, password, email, phone, verified) VALUES ($1, $2, $3, $4, $5, $6, DEFAULT);',
            values: [userId, regData.name, regData.lastname, regData.password, regData.email, regData.phone]
        };

        await performQuery(insertUserQuery);


        // Generate a URL from a hash the user + name + last + phone + email
        const toHashURL = regData.name + regData.lastname + regData.email + regData.phone;
        const hashedURL = await generateHashFromString(toHashURL);

        const insertURLQuery = {
            name: 'insertURLQuery',
            text: 'INSERT INTO public.signup_mail (url, usr_id, timestamp) VALUES ($1, $2, DEFAULT);',
            values: [hashedURL, userId]
        };

        try {
            // In case of error we must delete user from the user table
            await performQuery(insertURLQuery);

            // Load the template file
            let mailTemplateContents = fs.readFileSync(path.resolve('server/templates/registration_email.html'), {encoding: 'utf8'});


            const confirmationLink = process.env.THIS_HOST + '/confirmation/' + hashedURL;

            mailTemplateContents = mailTemplateContents.replace('##NAME', regData.name);
            mailTemplateContents = mailTemplateContents.replace('##LASTNAME', regData.lastname);
            mailTemplateContents = mailTemplateContents.replace('##LINK', confirmationLink);

            let mailOptions = {
                from: {
                    name: 'Registro de Livin',
                    address: process.env.MAILER_ADDRESS,
                },
                to: regData.email,
                subject: 'Confirma tu registro con Livin',
                html: mailTemplateContents
            };

            await mailerTransporter.sendMail(mailOptions, function (error, info) {
                if (error) {
                    return res.status(400).json(JSONError(3002, error))
                } else {
                    console.log(`Email sent to ${regData.email}:${info.response}`);
                }
            });

            return res.status(200).json(JSON.stringify({code: 0, message: 'success'}));

        } catch (error) {
            const deleteUserQuery = {
                name: 'deleteUserQuery',
                text: 'DELETE FROM public.user WHERE id = $1;',
                values: [userId]
            };

            // Delete the user from the database
            await performQuery(deleteUserQuery);
        }

    } catch (error) {
        return res.status(400).json(JSONError(-2000, error.message));
    }

    return res.status(400).json(JSONError(-500, 'Unknown'));
});

module.exports = router;

