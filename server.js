const express = require('express');
const session = require('express-session');
const axios = require('axios');
require("dotenv").config();

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

app.get('/login', async(req, res)=>{
    const message = req.query.message || null;
    res.render('login', {message: message});
});

app.post('/login', async(req, res)=>{
    const login = req.body.login || null;
    const password = req.body.password || null;
    if(login&&password){
        req.session.login = login;
        req.session.password = password;
        res.redirect('/home');
    }else{
        res.redirect('/login?message=Missing login or password');
    }
})

app.get('/home', (req, res) => {
    const error = req.query.error || null;
    res.render('home', { error });
});

app.post('/home', async (req, res) => {
    const baseUrl = process.env.API_BASE_URL;
    const { login, password } = req.session;
    const { profile_username } = req.body;
    const endpoint = req.body.media ? 'medias.php' : 'viewprofile.php';
    const url = `${baseUrl}${endpoint}`;

    try {
        const result = await axios.get(url, {
            params: {
                login,
                password,
                profile_username
            }
        });
        console.log(result.data);
        res.redirect('/home');
    } catch (error) {
        console.error(error);
        res.redirect(`/home?error=${encodeURIComponent(error.message)}`);
    }
});


app.listen('3000', () => {
    console.log("Server is running at localhost:3000");
});
