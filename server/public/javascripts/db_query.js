// General query

const {getPGPool} = require("./db_pool");

async function performQuery(queryData)
{
    const {pool} = getPGPool();
    try {
        const result = await pool.query(queryData);
        pool.release();
        return result;
    } catch (error) {
        console.error(`Error executing query: {$error}`);
        // Close the connection pool
        pool.end();
        // Throw the error to be caught by the caller
        throw error;
    }
}

module.exports = {performQuery};