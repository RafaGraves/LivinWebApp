// General query

const {getPGPool} = require("./db_pool");

async function performQuery(queryData)
{
    const pool = getPGPool();
    const client = await pool.connect();
    try {
        return await client.query(queryData);
    } catch (error) {
        console.error(`Error executing query: {$error}`);
        throw error;
    }
    finally {
        client.release();
    }
}

module.exports = {performQuery};