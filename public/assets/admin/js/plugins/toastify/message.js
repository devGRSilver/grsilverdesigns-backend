// ✔ SUCCESS TOAST
function successToast(message = "Success!") {
    Toastify({
        text: message,
        duration: 3000,
        close: true,
        gravity: "top",
        position: "center",
        style: {
            background: "#00cc4e",
            color: "#fff",
            fontWeight: "700",
            padding: "12px 20px",
            borderRadius: "10px"
        }
    }).showToast();
}


// ✔ ERROR TOAST
function errorToast(message = "Something went wrong!") {
    Toastify({
        text: message,
        duration: 3000,
        close: true,
        gravity: "top",
        position: "center",
        style: {
            background: "#ff4444",
            color: "#fff",
            fontWeight: "700",
            padding: "12px 20px",
            borderRadius: "10px"
        }
    }).showToast();
}


// ✔ WARNING TOAST
function warningToast(message = "Warning!") {
    Toastify({
        text: message,
        duration: 3000,
        close: true,
        gravity: "top",
        position: "center",
        style: {
            background: "#ffbb33",
            color: "#fff",
            fontWeight: "700",
            padding: "12px 20px",
            borderRadius: "10px"
        }
    }).showToast();
}
