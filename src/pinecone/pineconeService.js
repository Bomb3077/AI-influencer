const Pinecone = require('@pinecone-database/pinecone');
const { PINECONE_API_KEY } = require('./config');



async function initializePinecone() {
    await pinecone.init({
        environment: "gcp-starter",
        apiKey: PINECONE_API_KEY,
    });

    const index = pinecone.Index("first-index");
}


module.exports = {
    initializePinecone,
}