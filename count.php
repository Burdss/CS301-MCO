
<?php
// Start with empty text and zero statistics.
$text = "";
$counts = [
    "words" => 0,
    "letters" => 0,
    "characters" => 0,
    "sentences" => 0,
    "numbers" => 0,
    "symbols" => 0
];

// Analyze the submitted text using PHP.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $text = $_POST["text"] ?? "";

    // Count words separated by whitespace.
    preg_match_all('/\S+/u', $text, $wordMatches);
    $counts["words"] = count($wordMatches[0]);

    // Count letters, including letters with accents.
    preg_match_all('/\p{L}/u', $text, $letterMatches);
    $counts["letters"] = count($letterMatches[0]);

    // Count all characters, including spaces and line breaks.
    preg_match_all('/./us', $text, $characterMatches);
    $counts["characters"] = count($characterMatches[0]);

    // Count numeric digits.
    preg_match_all('/\d/u', $text, $numberMatches);
    $counts["numbers"] = count($numberMatches[0]);

    // Count punctuation marks that end sentences.
    preg_match_all('/[.!?]+(?=\s|$)/u', $text, $sentenceMatches);
    $counts["sentences"] = count($sentenceMatches[0]);

    // Count symbols that are not letters, numbers, or whitespace.
    preg_match_all('/[^\p{L}\p{N}\s]/u', $text, $symbolMatches);
    $counts["symbols"] = count($symbolMatches[0]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Text Analyzer - PHP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="analyzer">
        <h1>Text Analyzer</h1>
        <p class="subtitle">PHP Version</p>

        <!-- Show the results calculated by PHP -->
        <section class="stats">
            <div class="stat">
                <span><?= $counts["words"] ?></span>
                <p>Words</p>
            </div>
            <div class="stat">
                <span><?= $counts["letters"] ?></span>
                <p>Letters</p>
            </div>
            <div class="stat">
                <span><?= $counts["characters"] ?></span>
                <p>Characters</p>
            </div>
            <div class="stat">
                <span><?= $counts["sentences"] ?></span>
                <p>Sentences</p>
            </div>
            <div class="stat">
                <span><?= $counts["numbers"] ?></span>
                <p>Numbers</p>
            </div>
            <div class="stat">
                <span><?= $counts["symbols"] ?></span>
                <p>Symbols</p>
            </div>
        </section>

        <!-- Submit the text to this PHP page for analysis -->
        <form action="count.php" method="POST">
            <label for="text">Enter your text</label>
            <textarea
                id="text"
                name="text"
                placeholder="Type or paste your text here..."
            ><?= htmlspecialchars($text, ENT_QUOTES, "UTF-8") ?></textarea>

            <div class="buttons">
                <button type="submit">Analyze</button>
                <button type="button" id="saveBtn">Save</button>
                <button type="button" id="copyBtn">Copy</button>
                <button type="button" id="pasteBtn">Paste</button>
                <button type="button" id="clearBtn">Clear</button>
            </div>
        </form>

        <p class="message" id="message" aria-live="polite"></p>

    </main>

    <!-- These browser actions use JavaScript, not PHP counting logic -->
    <script>
        const textArea = document.getElementById("text");
        const message = document.getElementById("message");

        // Save the text as a file.
        document.getElementById("saveBtn").addEventListener("click", () => {
            const file = new Blob([textArea.value], { type: "text/plain" });
            const link = document.createElement("a");

            link.href = URL.createObjectURL(file);
            link.download = "my-text.txt";
            link.click();

            URL.revokeObjectURL(link.href);
            message.textContent = "Text saved.";
        });

        // Copy the text to the clipboard.
        document.getElementById("copyBtn").addEventListener("click", async () => {
            try {
                await navigator.clipboard.writeText(textArea.value);
                message.textContent = "Text copied.";
            } catch {
                message.textContent = "Copy failed. Check browser permissions.";
            }
        });

        // Paste text from the clipboard.
        document.getElementById("pasteBtn").addEventListener("click", async () => {
            try {
                textArea.value = await navigator.clipboard.readText();
                message.textContent = "Text pasted. Click Analyze to count it.";
            } catch {
                message.textContent = "Paste failed. Check browser permissions.";
            }
        });

        // Clear the text and reset the displayed results.
        document.getElementById("clearBtn").addEventListener("click", () => {
            textArea.value = "";

            // Submit an empty text value to reset the PHP results.
            const form = textArea.closest("form");
            const hidden = document.createElement("input");
            hidden.type = "hidden";
            hidden.name = "text";
            hidden.value = "";
            form.appendChild(hidden);
            form.submit();
        });
    </script>
</body>
</html>
