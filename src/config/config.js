require("dotenv").config();

module.exports = {
    PINECONE_API_KEY: process.env.PINECONE_API_KEY,
    CREDENTIALS: process.env.CREDENTIALS,
    API_BASE_URL: process.env.API_BASE_URL,
    SESSION_SECRET: 'asdoifphsodafjod', // or load from env
    // other configurations
};