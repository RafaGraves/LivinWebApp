// Create the database pool
const mysql = require('mysql2')
let mysqlPool = null;

function createPGPool() {
    return mysql.createPool({
        host: process.env.MYSQL_HOST,
        // port: parseInt(process.env.MYSQL_PORT, 10) || 5432,
        database: process.env.MYSQL_DBNAME,
        user: process.env.MYSQL_USER,
        password: process.env.MYSQL_PASSWORD,
        waitForConnections: true,
        connectionLimit: parseInt(process.env.MYSQL_MAX_CONNECTIONS, 10) || 5,
        queueLimit: 0
    });
}

function getMYSQLPool() {
    if (!mysqlPool) {
        mysqlPool = createPGPool();
    }
    return mysqlPool;
}

module.exports = {getPGPool: getMYSQLPool};