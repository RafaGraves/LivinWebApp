// Create the database pool
const {pg_pool} = require('pg');
let pgPool = null;

function createPGPool() {
    console.log(process.env.PG_HOST);
    return new pg_pool.Pool({
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