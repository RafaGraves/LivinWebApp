const express = require('express');
const router = express.Router();
const pgPool = require('../public/javascripts/db_pool')


/* GET users listing. */
router.get('/', function(req, res, next) {
    console.log('signup url');
    res.render('index', { title: 'Express' });
});

module.exports = router;

