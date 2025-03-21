document.addEventListener("DOMContentLoaded", () => {
    //Elements
    const roomList = document.getElementById("room-lists");
    const searchForm = document.getElementsById("search-form");

    // Function to fetch and display available rooms
    async function fetchAvailableRooms(checkInDate = null, checkOutDate = null) {
        try {
            const response = await fetch("searchRoom.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({check_in_date: checkInDate, check_out_date: checkOutDate})
            });
            const data = await response.json();

            roomList.innerHTML = ""; // Clear existing
            if (data.length === 0) {
                roomList.innerHTML = "<p>No rooms available for the selected dates.</p>";
                return;
            }

            data.forEach((room) => {
                const roomDiv = document.createElement("div");
                roomDiv.classList.add("room-card");
                roomDiv.innerHTML = `
                    <img src="${room.image_url}" alt="${room.name}" class="room-img">
                    <h3>${room.name}</h3>
                    <p>${room.description}</p>
                    <p>Price: KES ${room.price_per_night} / night</p>
                    <button class="btn" onclick="initiateBooking(${room.room_id}, '${room.name}', ${room.price_per_night})">Book Now</button>    
                `;
                roomList.appendChild(roomDiv);
            });
        } catch (error) {
            console.error("Error fetching rooms:", error);
            roomList.innerHTML = "<p>Error loading room. Please try again later.</p>";
        }
    }

    // Search functionality
    if (searchForm) {
        searchForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const checkInDate = document.getElementById("check-in-date").value;
            const checkOutDate = document.getElementById("check-out-date").value;
            fetchAvailableRooms(checkInDate, checkOutDate);
        });
    }

    // Funtion to handle booking intiation
    window.initiateBooking = async function (roomId, roomName, priceNight) {
        try {
            const userConfirmed = confirm (
                `You are about to book the ${roomName} room at KES ${pricePerNight} per night. \n Proceed to payment?`
            );
            if (!userConfirmed) return;

            const phone = prompt("Enter your MPESA phone number to proceed with payment");
            if (!phone) {
                alert("Payment cancelled.");
                return;
            }

            const paymentResponse = await fetch("mpesaPayment.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    phone: phone,
                    room_id: roomId
                })
            });

            const paymentData = await paymentResponse.json();
            if (paymentData.status === "success") {
                alert("Payment successful! Your booking is confirmed.");
                fetchAvailableRooms(); // Refresh room list
            } else {
                alert(`Payment failed: ${paymentData.message}`);
            }
        } catch (error) {
            console.error("Error processsing payment:", error);
            alert("An error occured during the payment process. Please try again.");
        }
    };

    // Fetch rooms on page load
    if (roomList) fetchAvailableRooms();
});