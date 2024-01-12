const axios = require('axios');
const fs = require('fs');
const fsp = require('fs').promises;
const path = require('path');
const potrace = require('potrace');


const downloadImagesFromData = async (mediaData) => {
    const downloadPromises = mediaData.Data.map(post => {
        return downloadImage(mediaData.UserID, post.PostID, post.DisplaySrc)
            .then(() => ({ status: 'success', postID: post.PostID }))
            .catch(error => ({ status: 'error', postID: post.PostID, error }));
    });

    const results = await Promise.all(downloadPromises);
    
    results.forEach(result => {
        if (result.status === 'success') {
            console.log(`Download complete for post ID ${result.postID}`);
        } else {
            console.error(`Download failed for post ID ${result.postID}:`, result.error);
        }
    });
};

const downloadImage = async (userID, postID, imageUrl) => {
    const destination = path.join(__dirname, `../../jpeg/${userID}_${postID}.jpeg`);
    try {
        const response = await axios({
            method: 'GET',
            url: imageUrl,
            responseType: 'stream'
        });
        const writer = fs.createWriteStream(destination);
        response.data.pipe(writer);
        return new Promise((resolve, reject) => {
            writer.on('finish', resolve);  
            writer.on('error', reject);
        });
    } catch (error) {
        console.error('Error downloading the image:', error);
        throw error;  // Rethrow the error for further handling
    }
};


const vectorizeJpegToSvg = async () => {
    const inputDir = path.join(__dirname, '../../jpeg/');
    const outputDir = path.join(__dirname, '../../svg/');
    try {
        const files = await fsp.readdir(inputDir);
        const imageFiles = files.filter(file => path.extname(file).toLowerCase() === '.jpeg');

        const promises = imageFiles.map(async (file) => {
            try {
                const inputFile = path.join(inputDir, file);
                const outputFile = path.join(outputDir, file.replace('.jpeg', '.svg'));

                const svg = await new Promise((resolve, reject) => {
                    potrace.trace(inputFile, (err, svg) => {
                        if (err) reject(err);
                        else resolve(svg);
                    });
                });

                await fsp.writeFile(outputFile, svg);
                console.log(`Converted ${file} to SVG.`);
            } catch (err) {
                console.error(`Error processing ${file}: ${err.message}`);
            }
        });

        await Promise.all(promises);
        console.log("finished converting");
    } catch (err) {
        console.error(`Error reading directory: ${err.message}`);
    }
};



module.exports = {
    downloadImagesFromData,
    vectorizeJpegToSvg,
};