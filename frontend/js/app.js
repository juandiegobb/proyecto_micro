const BASE_URL = 'http://127.0.0.1:8000/api';

// Variables globales
let selectedStudentId = null;

// Función para cargar los estudiantes en el dropdown
function loadStudents() {
    fetch(`${BASE_URL}/estudiantes`)
        .then(response => response.json())
        .then(data => {
            const studentDropdown = document.getElementById('students-dropdown');
            data.data.forEach(student => {
                const option = document.createElement('option');
                option.value = student.cod;
                option.textContent = `${student.cod} - ${student.nombres}`;
                studentDropdown.appendChild(option);
            });

            // Agregar evento para seleccionar estudiante
            studentDropdown.addEventListener('change', event => {
                selectedStudentId = event.target.value;
                fetchStudentDetails(selectedStudentId);
                fetchStudentGrades(selectedStudentId);
            });
        });
}

// Función para obtener los detalles de un estudiante
function fetchStudentDetails(studentId) {
    fetch(`${BASE_URL}/estudiantes/${studentId}`)
        .then(response => response.json())
        .then(data => {
            const studentInfo = document.getElementById('student-info');
            const status = data.estado === 'aprobado' ? 'Aprobado' : 'Reprobado';
            studentInfo.innerHTML = `
                Código: ${data.cod} <br>
                Nombre: ${data.nombres} <br>
                Email: ${data.email} <br>
                Estado: ${status} <br>
                Promedio: ${data.promedio ? data.promedio.toFixed(2) : 'No hay nota'}
            `;
        });
}

// Función para obtener las notas de un estudiante
function fetchStudentGrades(studentId) {
    fetch(`${BASE_URL}/notas?codEstudiante=${studentId}`)
        .then(response => response.json())
        .then(data => {
            const gradesList = document.getElementById('grades-list');
            gradesList.innerHTML = '';
            data.data.forEach(grade => {
                const gradeDiv = document.createElement('div');
                gradeDiv.classList.add('grade-item');
                gradeDiv.textContent = `Actividad: ${grade.actividad} - Nota: ${grade.nota}`;
                
                // Clasificar las notas
                if (grade.nota <= 2) {
                    gradeDiv.classList.add('low-grade');
                } else if (grade.nota > 2 && grade.nota < 3) {
                    gradeDiv.classList.add('medium-low-grade');
                } else if (grade.nota >= 3 && grade.nota < 4) {
                    gradeDiv.classList.add('medium-high-grade');
                } else {
                    gradeDiv.classList.add('high-grade');
                }

                gradesList.appendChild(gradeDiv);
            });

            updateGradesSummary(data.data);
        });
}

// Función para actualizar el resumen de notas
function updateGradesSummary(grades) {
    const below3 = grades.filter(grade => grade.nota < 3).length;
    const above3 = grades.filter(grade => grade.nota >= 3).length;

    document.getElementById('below-3').textContent = below3;
    document.getElementById('above-3').textContent = above3;
}

// Función para registrar o modificar una nota
function handleNoteFormSubmit(event) {
    event.preventDefault();
    const activity = document.getElementById('activity').value;
    const grade = parseFloat(document.getElementById('grade').value);

    if (isNaN(grade) || grade < 0 || grade > 5) {
        alert('La nota debe estar entre 0 y 5.');
        return;
    }

    const noteData = {
        actividad: activity,
        nota: grade,
        codEstudiante: selectedStudentId
    };

    // Verificar si estamos creando o actualizando la nota
    const noteId = document.getElementById('note-id') ? document.getElementById('note-id').value : null;
    const url = noteId ? `${BASE_URL}/notas/${noteId}` : `${BASE_URL}/notas`;

    fetch(url, {
        method: noteId ? 'PUT' : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(noteData)
    })
    .then(response => response.json())
    .then(data => {
        alert(noteId ? 'Nota actualizada' : 'Nota registrada');
        fetchStudentGrades(selectedStudentId);
    })
    .catch(error => console.error('Error al registrar/modificar la nota:', error));
}

// Función para eliminar una nota
function handleDeleteNote() {
    const confirmDelete = confirm('¿Estás seguro de que quieres eliminar esta nota?');
    if (confirmDelete && selectedStudentId) {
        // Aquí se podría eliminar la nota seleccionada
    }
}

// Inicialización
document.addEventListener('DOMContentLoaded', () => {
    loadStudents();
    document.getElementById('note-form').addEventListener('submit', handleNoteFormSubmit);
    document.getElementById('delete-grade-button').addEventListener('click', handleDeleteNote);
});
