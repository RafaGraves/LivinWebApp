const express = require('express');
const router = express.Router();

const {performQuery} = require('../public/javascripts/db_query')// {performQuery} from ""


/* GET users listing. */
router.get('/:id_config', async function (req, res, next) {
    console.log(`Confirmation requested for ID: ${req.params.id_config}`);

    // This query returns the time difference in minutes
    try {
        const query = {
            name: 'signupConfQuery',
            text: 'SELECT sp.url,EXTRACT(EPOCH FROM (NOW() - sp.timestamp)) / 60 AS diff FROM test_db_living.public.signup_mail AS sp WHERE sp.usr_id = $1',
            values: [req.params.id_config]
        }

        const res = await performQuery(query);

      //  if (res.rowCount === 0) {
            res.render('confirmation_missing');
      //  }
    } catch (error) {
        console.error(error.message);
    }

    res.render('index', {title: 'Express'});
});

module.exports = router;


