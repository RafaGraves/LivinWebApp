const createError = require('http-errors');
const express = require('express');
const path = require('path');
const cookieParser = require('cookie-parser');
const logger = require('morgan');
const cors = require('cors');
const bodyParser = require('body-parser');

// Load .env file
require('dotenv').config({path: __dirname + '/.env'});

const indexRouter = require('./routes/index');
const usersRouter = require('./routes/users');
const registerRouter = require('./routes/register')
const confirmationRouter = require('./routes/confirmation')

const PORT = parseInt(process.env.NODE_SERVER_PORT, 10) || 4500;

const server = express();

// view engine setup
server.set('views', path.join(__dirname, 'views'));
server.set('view engine', 'ejs');

server.use(logger('dev'));
server.use(express.json());
server.use(express.urlencoded({extended: false}));
server.use(cookieParser());
server.use(express.static(path.join(__dirname, 'public')));
server.use(bodyParser.json());

server.use('/', indexRouter);
server.use('/users', usersRouter);
server.use('/api/signup', cors({
    origin: 'http://localhost:63342',
    methods: ['POST'],
    allowedHeaders: ['Content-Type']
}), registerRouter);
server.use('/confirmation', confirmationRouter);

// catch 404 and forward to error handler
server.use(function (req, res, next) {
    next(createError(404));
});


// error handler
server.use(function (err, req, res, next) {
    // set locals, only providing error in development
    res.locals.message = err.message;
    res.locals.error = req.app.get('env') === 'development' ? err : {};

    // render the error page
    res.status(err.status || 500);
    res.render('error');
});


server.listen(PORT, () => {
    console.log(`R_ESTATE server running on port ${PORT}`);
})

module.exports = server;
