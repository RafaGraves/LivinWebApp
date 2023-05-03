// Create the database pool
const { Pool } = require('pg')
let pgPool = null;

function createPGPool() {
    return new Pool({
        host: process.env.PG_HOST,
        port: parseInt(process.env.PG_PORT, 10) || 5432,
        database: process.env.PG_DBNAME,
        user: process.env.PG_USER,
        password: process.env.PG_PASSWORD,
        connectionTimeoutMillis: 0,
        idleTimeoutMillis: 0,
        max: parseInt(process.env.PG_MAX_CONNECTIONS, 10) || 5
    });
}

function getPGPool() {
    if (!pgPool) {
        pgPool = createPGPool();
    }
    return pgPool;
}

module.exports = {getPGPool};