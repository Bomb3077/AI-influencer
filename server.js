const express = require('express');
const session = require('express-session');
const axios = require('axios');
require("dotenv").config();
const fs = require('fs');
const CREDENTIALS = process.env.CREDENTIALS;

// Initialize and configure Express
const app = express();
app.set('view engine', 'ejs');
app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(session({
    secret: 'asdoifphsodafjod', // This should be a long, random string to keep sessions secure
    resave: false, // Don't save session if unmodified
    saveUninitialized: false, // Don't create session until something is stored
    cookie: { secure: false } // Use cookies over HTTPS only
}));

app.get('/', (req, res) => {
    const error = req.query.error || null;
    res.render('home', { error });
});

app.post('/', async (req, res) => {
    const baseUrl = process.env.API_BASE_URL;
    const { profile_username, limit } = req.body;
    const endpoint = req.body.media ? 'medias.php' : 'viewprofile.php';
    const url = `${baseUrl}${endpoint}`;

    try {
        const result = await axios.get(url, {
            params: {
                CREDENTIALS,
                profile_username,
                limit
            }
        });
        console.log(result.data);
        downloadImagesFromData(result.data);
        res.redirect('/');
    } catch (error) {
        console.error(error);
        res.redirect(`/?error=${encodeURIComponent(error.message)}`);
    }
});

const downloadImagesFromData = (mediaData) =>{
    mediaData.Data.forEach(post => {
        downloadImage(mediaData.UserID, post.PostID, post.DisplaySrc)
        .then(() => console.log('Download complete'))
        .catch(error => console.error('Download failed:', error));
    })
};

async function downloadImage(userID, postID, imageUrl) {
    const path = `./jpeg/${userID}_${postID}.jpeg`;
    try {
        const response = await axios({
            method: 'GET',
            url: imageUrl,
            responseType: 'stream'
        });
        const writer = fs.createWriteStream(path);
        response.data.pipe(writer);
        return new Promise((resolve, reject) => {
            writer.on('finish', resolve);  
            writer.on('error', reject);
        });
    }catch(error){
        console.error('Error downloading the image:', error);
        throw error;  // Rethrow the error for further handling
    }
};

app.listen('3000', () => {
    console.log("Server is running at localhost:3000");
});
