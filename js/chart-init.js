document.addEventListener("DOMContentLoaded", function () {
  // Expense Chart
  if (document.getElementById("expenseChart")) {
    fetch("get-expense-data.php")
      .then((response) => response.json())
      .then((data) => {
        const ctx = document.getElementById("expenseChart").getContext("2d");
        new Chart(ctx, {
          type: "line",
          data: {
            labels: data.labels,
            datasets: [
              {
                label: "Expenses",
                data: data.values,
                borderColor: "#2196F3",
                backgroundColor: "rgba(33, 150, 243, 0.1)",
                fill: true,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
          },
        });
      });
  }

  // Category Chart
  if (document.getElementById("categoryChart")) {
    fetch("get-category-data.php")
      .then((response) => response.json())
      .then((data) => {
        const ctx = document.getElementById("categoryChart").getContext("2d");
        new Chart(ctx, {
          type: "doughnut",
          data: {
            labels: data.labels,
            datasets: [
              {
                data: data.values,
                backgroundColor: [
                  "#2196F3",
                  "#4CAF50",
                  "#FFC107",
                  "#F44336",
                  "#9C27B0",
                  "#00BCD4",
                  "#FF5722",
                  "#795548",
                ],
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
          },
        });
      });
  }
});
