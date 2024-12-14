const express = require("express");
const bodyParser = require("body-parser");
const axios = require("axios");
const cors = require("cors"); // Import CORS middleware

const app = express();

// Konfigurasi CORS untuk mengizinkan permintaan dari ecommerce.test
app.use(cors({
    origin: 'http://ecommerce.test', // Ganti ini dengan domain yang diizinkan
    methods: ['GET', 'POST'],
    allowedHeaders: ['Content-Type', 'Authorization']
}));

app.use(bodyParser.json({ limit: "10mb" })); // Batas untuk data gambar besar

app.post("/classify", async (req, res) => {
    try {
        const { imageBase64 } = req.body;

        // Kirim gambar ke API Roboflow
        const response = await axios.post(
            "https://detect.roboflow.com/pbkk-book-search/3",
            imageBase64,
            {
                params: {
                    api_key: "Y2RhFee09f8bvvheivuV" // Ganti dengan API key Roboflow Anda
                },
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                }
            }
        );

        res.json(response.data);
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ error: "Failed to process the image" });
    }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`Node.js server running on port ${PORT}`);
});
