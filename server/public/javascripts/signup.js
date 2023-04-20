import {performQuery} from "./db_query";
import {JSONError} from "./cje";

const crypto = require('crypto')

//import {getPGPool} from "./db_pool";

async function isEmailRegistered(email) {
    const emailQuery = {
        name: 'isEmailRegistered',
        text: 'SELECT email FROM user WHERE email=$1;',
        values: [email]
    };

    try {
        const res = await performQuery(emailQuery);
        return res.rows !== 0;
    } catch (error) {
        throw new JSONError(-2000, error);
    }
}

// psw parameter must be hashed by this time
async function createUser(name, psw, email) {
    try {
        // Detect if the email is already registered
        const reg = await isEmailRegistered();
        if (reg === true) {
            return JSONError(1001, 'email provided is already registered');
        }

        // Hash the id
        const idToHash = name + email;
        const hashFunction = crypto.createHash('sha1');
        hashFunction.update(idToHash);
        const idHashString = hashFunction.digest().toString();

        // Insertion query
        const signUpQuery = {
            name: 'signUpQuery',
            text: 'INSERT INTO test_db_living.public.user (id, name, password, email, verified) VALUES ($1, $2, $3, $4, DEFAULT);',
            values: [idHashString, name, psw, email]
        };

        const insResponse = await performQuery(signUpQuery);

        // Generate another sha1 based on the email
        // this new sha1 will be stored in the signup_mail database table
        // and this is done in order to confirm the user

        hashFunction.update(email);
        const mailHashString = hashFunction.digest().toString();

        const signupMailQuery = {
            name: 'signupMailQuery',
            text: 'INSERT INTO signup_mail (usr_id, url, timestamp) VALUES ($1, $2, DEFAULT);',
            values: [idHashString, mailHashString]
        };

        const supQuery = await performQuery(signupMailQuery);

        const response = {
            code: 0,
            message: ''
        };

        return JSON.stringify(response);

    } catch (e) {
        if (e.name === 'JSONError') {
            console.error(e.message);
            return e;
        } else {
            console.error(e.message);
            return JSONError(-1000, e.message);
        }
    }
}


