app.post("/api/contact", async (req, res) => {
    const { name, phone, email, subject, message } = req.body;

    try {
        const transporter = nodemailer.createTransport({
            service: "gmail",
            auth: {
                user: "mamskusinacontact@gmail.com",
                pass: "ttsrvfuedkczvvep"
            }
        });

        await transporter.sendMail({
            from: email,
            to: "mamskusinacontact@gmail.com",
            subject: `Nieuw bericht: ${subject}`,
            text: `
Naam: ${name}
Telefoon: ${phone}
Email: ${email}

Bericht:
${message}
            `
        });

        res.json({ success: true });
    } catch (error) {
        console.error(error);
        res.json({ success: false, message: "Kon e-mail niet verzenden." });
    }
});