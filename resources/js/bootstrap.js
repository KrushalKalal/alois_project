const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    console.log("CSRF Token Found:", token.content);
    window.Laravel = { csrfToken: token.content };
} else {
    console.error("CSRF Token Not Found");
}
