const express = require('express');
const router = express.Router();
const axios = require('axios');
const {vectorizeJpegToSvg} = require('../controllers/imageController');
const { fetchProfileData } = require('../controllers/profileController');
const {CREDENTIALS} = require('../config/config');



router.get('/', (req, res) => {
    const error = req.query.error || null;
    res.render('home', { error });
});


router.post('/', fetchProfileData);

router.get('/vectorize', (req, res) => {
    res.redirect('/');
});

router.post('/vectorize', (req, res) => {
    vectorizeJpegToSvg();
    res.redirect('/');
});

module.exports = router;