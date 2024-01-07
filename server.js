import express from 'express';
import session from 'express-session';
import axios from 'axios';

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
    const message = req.query.message ?? null;
    res.render('login', {message: message});
});

app.post('/login', async(req, res)=>{
    const login = req.body.login ?? null;
    const password = req.body.password ?? null;
    if(login&&password){
        req.session.login = login;
        req.session.password = password;
        res.redirect('/home');
    }else{
        res.redirect('/login?message=Missing login or password');
    }
})

app.get('/home', async(req, res)=>{
    const data = req.query.data ?? null;
    const error = req.query.error ?? null;
    res.render('home', {data: data, error: error});
});
app.post('/home', async(req, res)=>{
    axios.get('http://instai:8888/php/viewprofile.php?'+
    'login='+req.session.login+"&password="+req.session.password+
    "&profile_username="+req.body.profile)
    .then(result => {
        console.log(result.data);
        res.redirect('/home?data='+result.data);
    })
    .catch(error=>{
        res.redirect('/home?error='+error);
    });
})

app.listen('3000', () => {
    console.log("Server is running at localhost:3000");
});
