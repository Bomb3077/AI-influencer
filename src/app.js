const express = require('express');
const session = require('express-session');
const indexRoutes = require('./routes/indexRoute');

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

app.use('/', indexRoutes);

app.listen('3000', () => {
    console.log("Server is running at localhost:3000");
});
