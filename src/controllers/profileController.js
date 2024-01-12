const axios = require('axios');
const { downloadImagesFromData } = require('./imageController');

const fetchProfileData = async (req, res) => {
    const baseUrl = process.env.API_BASE_URL;
    const { profile_username, limit } = req.body;
    const endpoint = req.body.media ? 'medias.php' : 'viewprofile.php';
    const url = `${baseUrl}${endpoint}`;

    try {
        const result = await axios.get(url, {
            params: {
                CREDENTIALS: process.env.CREDENTIALS,
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
};

module.exports = {
    fetchProfileData
};
