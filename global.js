const toggleBtn = document.getElementById("toggle-btn");
const nav = document.querySelector("nav");

// Load saved state on page load
const isCollapsed = localStorage.getItem("nav-collapsed") === "true";
if (isCollapsed) nav.classList.add("collapsed");

// Handle toggle click
toggleBtn.addEventListener("click", () => {
    nav.classList.toggle("collapsed");

    // Save the state in localStorage
    localStorage.setItem("nav-collapsed", nav.classList.contains("collapsed"));
});

const tabs = document.querySelectorAll(".tab-btn");
const contents = document.querySelectorAll(".tab-content");

if (tabs) {
    tabs.forEach((tab) => {
        tab.addEventListener("click", () => {
            const target = tab.getAttribute("data-tab");

            // reset all tabs
            tabs.forEach((t) => {
                t.classList.remove("active", "bg-white", "text-gray-800", "border", "border-b-0", "border-gray-200");
                t.classList.add("bg-gray-100", "text-gray-600");
            });

            // show clicked tab
            tab.classList.add("active", "bg-white", "text-gray-800", "border", "border-b-0", "border-gray-200");
            tab.classList.remove("bg-gray-100", "text-gray-600");

            // hide/show content
            contents.forEach((c) => c.classList.add("hidden"));
            document.getElementById(target).classList.remove("hidden");
        });
    });
}
