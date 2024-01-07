import { NodePHP } from '@php-wasm/node';
import express from 'express';
import session from 'express-session';

// Initialize and configure PHP
const php = await NodePHP.load('8.2', {
    requestHandler: {
        documentRoot: '/srv',
        absoluteUrl: "http://localhost:3000"
    }
});

php.mkdir('/srv');
php.chdir('/srv');
php.mount(process.cwd(), '/srv'); // Ensure this is the correct path

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


app.post('/login', async (req, res) => {
    const login = req.body.login || null;
    const password = req.body.password || null;
    if (!login || !password){
        console.log("missing login and password");
        res.redirect('login.ejs');
    }
    req.session.login = login;
    req.session.password = password;
    res.redirect("/viewprofile");
});
app.get('/viewprofile', async(req, res)=>{
    console.log("in viewprofile");
    console.log(req.session.login+req.session.password);
    if (req.session.login && req.session.password) {
        const query = `?login=${encodeURIComponent(req.session.login)}&password=${encodeURIComponent(req.session.password)}`;
        res.redirect("viewprofile.php" + query);
    } else {
        res.redirect("/login.ejs"); // Redirect to login if the session data isn't available
    }
});

app.all('*', async (req, res) => {
    console.log(req.method + ' - ' + req.url);

    const response = await php.request({
        method: req.method,
        url: req.url,
        headers: req.headers,
        body: req.body
    });

    res.status(response.httpStatusCode).send(response.text);
});

// Start the server
app.listen('3000', () => {
    console.log("Server is running at localhost:3000");
});
