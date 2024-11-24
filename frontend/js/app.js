document.addEventListener("DOMContentLoaded", function () {
    fetchStudents(); // Cargar estudiantes al inicio
    fetchGeneralStatistics(); // Cargar estadísticas generales
});

// Mostrar el formulario para agregar estudiantes
function showAddForm() {
    document.getElementById("form-title").innerText = "Agregar Estudiante";
    document.getElementById("student-form").reset();
    document.getElementById("form-modal").style.display = "block";
}

// Cerrar el formulario/modal
function closeForm() {
    document.getElementById("form-modal").style.display = "none";
}

// Filtrar estudiantes por código, nombre, email, estado o rango de nota
function filterStudents() {
    const filterText = document.getElementById("filter").value.toLowerCase();
    const rows = document.querySelectorAll("#students-table-body tr");
    let found = false;

    rows.forEach(row => {
        const code = row.cells[0].innerText.toLowerCase();
        const name = row.cells[1].innerText.toLowerCase();
        const email = row.cells[2].innerText.toLowerCase();
        const status = row.cells[4].innerText.toLowerCase();
        const grade = row.cells[3].innerText.toLowerCase();

        if (code.includes(filterText) || name.includes(filterText) || email.includes(filterText) || status.includes(filterText) || grade.includes(filterText)) {
            row.style.display = "";
            found = true;
        } else {
            row.style.display = "none";
        }
    });

    // Mostrar mensaje si no hay resultados
    const noResults = document.getElementById("no-results");
    if (!found) {
        noResults.style.display = "block";
    } else {
        noResults.style.display = "none";
    }
}

// Obtener todos los estudiantes
function fetchStudents() {
    fetch("http://127.0.0.1:8000/api/estudiantes")
        .then(response => response.json())
        .then(data => {
            const students = data.data;
            const tableBody = document.getElementById("students-table-body");
            tableBody.innerHTML = "";

            students.forEach(student => {
                const status = student.promedio >= 3 ? "Aprobado" : (student.promedio ? "Reprobado" : "No hay nota");
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${student.cod}</td>
                    <td>${student.nombres}</td>
                    <td>${student.email}</td>
                    <td>${student.promedio || "No hay nota"}</td>
                    <td>${status}</td>
                    <td class="actions">
                        <button onclick="showEditForm(${student.id}, '${student.cod}', '${student.nombres}', '${student.email}')">Editar</button>
                        <button onclick="deleteStudent(${student.id})">Eliminar</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            fetchGeneralStatistics();
        })
        .catch(error => console.error("Error al obtener estudiantes:", error));
}

// Mostrar el formulario de edición
function showEditForm(id, cod, nombres, email) {
    document.getElementById("form-title").innerText = "Editar Estudiante";
    document.getElementById("student-code").value = cod;
    document.getElementById("student-code").dataset.id = id;
    document.getElementById("student-name").value = nombres;
    document.getElementById("student-email").value = email;
    document.getElementById("form-modal").style.display = "block";
}

// Guardar o actualizar estudiante
document.getElementById("student-form").addEventListener("submit", function (event) {
    event.preventDefault();

    const studentData = {
        cod: document.getElementById("student-code").value,
        nombres: document.getElementById("student-name").value,
        email: document.getElementById("student-email").value
    };

    const studentId = document.getElementById("student-code").dataset.id;
    if (studentId) {
        updateStudent(studentId, studentData);
    } else {
        addStudent(studentData);
    }

    closeForm();
});

// Función para agregar estudiante
function addStudent(studentData) {
    if (!validateEmail(studentData.email)) {
        showMessage("El email no es válido", true);
        return;
    }

    fetch("http://127.0.0.1:8000/api/estudiantes", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(studentData)
    })
    .then(response => response.json())
    .then(() => {
        showMessage("Estudiante agregado correctamente");
        fetchStudents(); // Recargar la lista
    })
    .catch(error => {
        console.error("Error al agregar estudiante:", error);
        showMessage("Error al agregar estudiante", true);
    });
}

// Función para actualizar estudiante
function updateStudent(studentId, studentData) {
    if (!validateEmail(studentData.email)) {
        showMessage("El email no es válido", true);
        return;
    }

    fetch(`http://127.0.0.1:8000/api/estudiantes/${studentId}`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(studentData)
    })
    .then(response => response.json())
    .then(() => {
        showMessage("Estudiante actualizado correctamente");
        fetchStudents(); // Recargar la lista
    })
    .catch(error => {
        console.error("Error al actualizar estudiante:", error);
        showMessage("Error al actualizar estudiante", true);
    });
}

// Función para eliminar estudiante
function deleteStudent(studentId) {
    if (confirm("¿Seguro que quieres eliminar este estudiante?")) {
        fetch(`http://127.0.0.1:8000/api/estudiantes/${studentId}`, {
            method: "DELETE"
        })
        .then(() => {
            fetchStudents(); // Recargar la lista
        })
        .catch(error => console.error("Error al eliminar estudiante:", error));
    }
}

// Función para mostrar mensajes de éxito
function showMessage(message, isError = false) {
    const messageElement = document.createElement("p");
    messageElement.textContent = message;
    messageElement.style.color = isError ? "red" : "green";
    document.body.appendChild(messageElement);
    setTimeout(() => {
        messageElement.remove();
    }, 3000);
}

// Recargar los estudiantes manualmente
function reloadStudents() {
    fetchStudents();
}

// Validación de email
function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Función para mostrar estadísticas generales
function fetchGeneralStatistics() {
    fetch("http://127.0.0.1:8000/api/estudiantes/statistics")
        .then(response => response.json())
        .then(data => {
            const stats = data.data;
            document.getElementById("approved-count").innerText = stats.approved;
            document.getElementById("failed-count").innerText = stats.failed;
            document.getElementById("no-grade-count").innerText = stats.no_grade;
        })
        .catch(error => console.error("Error al obtener estadísticas:", error));
}
