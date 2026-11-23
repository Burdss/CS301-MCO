
const textInput = document.getElementById("textInput");
const message = document.getElementById("message");

// Get the text and count its contents.
function analyzeText() {
    const text = textInput.value;

    // Count words separated by spaces or line breaks.
    const words = text.trim() === "" ? [] : text.trim().split(/\s+/u);

    // Count letters, numbers, and symbols.
    const letters = text.match(/\p{L}/gu) || [];
    const numbers = text.match(/\d/gu) || [];
    const symbols = text.match(/[^\p{L}\p{N}\s]/gu) || [];
    

    // Count sentences ending with a period, question mark, or exclamation mark.
    const sentences = text.match(/[.!?]+(?=\s|$)/gu) || [];

    // Update all statistics on the page.
    document.getElementById("words").textContent = words.length;
    document.getElementById("letters").textContent = letters.length;
    document.getElementById("characters").textContent = [...text].length;
    document.getElementById("sentences").textContent = sentences.length;
    document.getElementById("numbers").textContent = numbers.length;
    document.getElementById("symbols").textContent = symbols.length;
    
    
}

// Recount automatically whenever the user types.
textInput.addEventListener("input", analyzeText);

// Save the text as a .txt file.
document.getElementById("saveBtn").addEventListener("click", () => {
    const file = new Blob([textInput.value], { type: "text/plain" });
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
        await navigator.clipboard.writeText(textInput.value);
        message.textContent = "Text copied.";
    } catch {
        message.textContent = "Copy failed. Check browser permissions.";
    }
});

// Paste text from the clipboard.
document.getElementById("pasteBtn").addEventListener("click", async () => {
    try {
        textInput.value = await navigator.clipboard.readText();
        analyzeText();
        message.textContent = "Text pasted.";
    } catch {
        message.textContent = "Paste failed. Check browser permissions.";
    }
});

// Clear the text area and reset the statistics.
document.getElementById("clearBtn").addEventListener("click", () => {
    textInput.value = "";
    analyzeText();
    message.textContent = "Text cleared.";
});

// Display zeroes when the page first opens.
analyzeText();
