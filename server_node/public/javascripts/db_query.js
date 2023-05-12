// General query

const {getMYSQLPool} = require("./db_pool");

async function performQuery(statement, values)
{
    const pool = getMYSQLPool();
    const client = await pool.connect();
    try {
        return await client.query(statement, values);
    } catch (error) {
        console.error(`Error executing query: {$error}`);
        throw error;
    }
    finally {
        client.release();
    }
}

module.exports = {performQuery};