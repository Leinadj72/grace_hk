// Disable right-click context menu
document.addEventListener("contextmenu", (e) => e.preventDefault());

// Block common keyboard shortcuts
document.addEventListener("keydown", function (e) {
  // Block F12, Ctrl+Shift+I/J/C/U, PrintScreen
  if (
    e.key === "F12" ||
    (e.ctrlKey &&
      e.shiftKey &&
      ["I", "J", "C"].includes(e.key.toUpperCase())) ||
    (e.ctrlKey && ["U", "S", "P"].includes(e.key.toUpperCase()))
  ) {
    e.preventDefault();
  }

  // Attempt to block PrintScreen
  if (e.key === "PrintScreen") {
    navigator.clipboard.writeText(""); // Try clearing clipboard
    alert("📸 Screenshot blocked!");
  }
});

// Disable copy, cut, drag
document.addEventListener("copy", (e) => e.preventDefault());
document.addEventListener("cut", (e) => e.preventDefault());
document.addEventListener("dragstart", (e) => e.preventDefault());

// Blur screen on focus loss (anti-screen record)
const blurOverlay = document.createElement("div");
blurOverlay.style = `
  position:fixed;
  top:0; left:0; right:0; bottom:0;
  background:#000000cc;
  color:white;
  display:none;
  justify-content:center;
  align-items:center;
  font-size:1.5rem;
  z-index:9999;
  text-align:center;
  padding: 2rem;
`;
blurOverlay.innerHTML =
  "🔒 Viewing restricted. Please return to the tab to continue.";
document.body.appendChild(blurOverlay);

window.onblur = () => (blurOverlay.style.display = "flex");
window.onfocus = () => (blurOverlay.style.display = "none");
