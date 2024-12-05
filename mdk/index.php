
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "school_management";

// Соединение с базой данных
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Функция для вывода ошибок
function showError($message) {
    echo "<div style='color: red;'>$message</div>";
}

// Функция для вывода успеха
function showSuccess($message) {
    echo "<div style='color: green;'>$message</div>";
}

// 1. Подключение к базе данных
if (isset($_GET['action']) && $_GET['action'] == 'connect') {
    if ($conn) {
        showSuccess("Соединение установлено успешно");
    } else {
        showError("Ошибка подключения: " . $conn->connect_error);
    }
}

// 2. Добавление нового студента
if (isset($_POST['add_student'])) {
    $name = $_POST['student_name'];
    $group_id = $_POST['group_id'];

    $sql = "INSERT INTO students (name, group_id) VALUES ('$name', $group_id)";
    if ($conn->query($sql) === TRUE) {
        showSuccess("Студент добавлен успешно");
    } else {
        showError("Ошибка: " . $conn->error);
    }
}

// 3. Вывод списка студентов
if (isset($_GET['action']) && $_GET['action'] == 'list_students') {
    $sql = "SELECT * FROM students";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table><tr><th>ID</th><th>Имя</th><th>Группа</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["id"]. "</td><td>" . $row["name"]. "</td><td>" . $row["group_id"]. "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 результатов";
    }
}

// 4. Добавление группы
if (isset($_POST['add_group'])) {
    $name = $_POST['group_name'];

    $sql = "INSERT INTO groups (name) VALUES ('$name')";
    if ($conn->query($sql) === TRUE) {
        showSuccess("Группа добавлена успешно");
    } else {
        showError("Ошибка: " . $conn->error);
    }
}

// 5. Привязка студента к группе
if (isset($_POST['assign_group'])) {
    $student_id = $_POST['student_id'];
    $group_id = $_POST['group_id'];

    $sql = "UPDATE students SET group_id = $group_id WHERE id = $student_id";
    if ($conn->query($sql) === TRUE) {
        showSuccess("Студент привязан к группе успешно");
    } else {
        showError("Ошибка: " . $conn->error);
    }
}

// 6. Вывод студентов с их группами
if (isset($_GET['action']) && $_GET['action'] == 'list_students_with_groups') {
    $sql = "SELECT students.name AS student_name, groups.name AS group_name 
            FROM students 
            LEFT JOIN groups ON students.group_id = groups.id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table><tr><th>Имя Студента</th><th>Название Группы</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["student_name"]. "</td><td>" . $row["group_name"]. "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 результатов";
    }
}

// 7. Регистрация студента на курс
if (isset($_POST['register_course'])) {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];

    $sql = "INSERT INTO student_courses (student_id, course_id) VALUES ($student_id, $course_id)";
    if ($conn->query($sql) === TRUE) {
        showSuccess("Студент зарегистрирован на курс успешно");
    } else {
        showError("Ошибка: " . $conn->error);
    }
}

// 8. Вывод всех курсов с количеством студентов
if (isset($_GET['action']) && $_GET['action'] == 'list_courses_with_students') {
    $sql = "SELECT courses.name AS course_name, COUNT(student_courses.student_id) AS student_count 
            FROM courses 
            LEFT JOIN student_courses ON courses.id = student_courses.course_id 
            GROUP BY courses.name";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table><tr><th>Название Курса</th><th>Количество Студентов</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["course_name"]. "</td><td>" . $row["student_count"]. "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 результатов";
    }
}

// 9. Удаление студента
if (isset($_POST['delete_student'])) {
    $student_id = $_POST['student_id'];

    $sql = "DELETE FROM students WHERE id = $student_id";
    if ($conn->query($sql) === TRUE) {
        showSuccess("Студент удален успешно");
    } else {
        showError("Ошибка: " . $conn->error);
    }
}

// 10. Обновление имени студента
if (isset($_POST['update_student_name'])) {
    $student_id = $_POST['student_id'];
    $name = $_POST['name'];

    $sql = "UPDATE students SET name = '$name' WHERE id = $student_id";
    if ($conn->query($sql) === TRUE) {
        showSuccess("Имя студента обновлено успешно");
    } else {
        showError("Ошибка: " . $conn->error);
    }
}

// 11. Вывод преподавателей и их курсов
if (isset($_GET['action']) && $_GET['action'] == 'list_teachers_with_courses') {
    $sql = "SELECT teachers.name AS teacher_name, courses.name AS course_name 
            FROM teachers 
            LEFT JOIN courses ON teachers.id = courses.teacher_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table><tr><th>Имя Преподавателя</th><th>Название Курса</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["teacher_name"]. "</td><td>" . $row["course_name"]. "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 результатов";
    }
}

// 12. Поиск студента по имени
if (isset($_POST['search_student'])) {
    $name = $_POST['name'];

    $sql = "SELECT * FROM students WHERE name LIKE '%$name%'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table><tr><th>ID</th><th>Имя</th><th>Группа</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["id"]. "</td><td>" . $row["name"]. "</td><td>" . $row["group_id"]. "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 результатов";
    }
}

// 13. Вывод студентов без группы
if (isset($_GET['action']) && $_GET['action'] == 'list_students_without_group') {
    $sql = "SELECT * FROM students WHERE group_id IS NULL";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table><tr><th>ID</th><th>Имя</th><th>Группа</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["id"]. "</td><td>" . $row["name"]. "</td><td>" . $row["group_id"]. "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 результатов";
    }
}

// 14. Добавление нового курса
if (isset($_POST['add_course'])) {
    $name = $_POST['course_name'];
    $teacher_id = $_POST['teacher_id'];

    $sql = "INSERT INTO courses (name, teacher_id) VALUES ('$name', $teacher_id)";
    if ($conn->query($sql) === TRUE) {
        showSuccess("Курс добавлен успешно");
    } else {
        showError("Ошибка: " . $conn->error);
    }
}

// 15. Регистрация нового преподавателя
if (isset($_POST['add_teacher'])) {
    $name = $_POST['teacher_name'];

    $sql = "INSERT INTO teachers (name) VALUES ('$name')";
    if ($conn->query($sql) === TRUE) {
        showSuccess("Преподаватель добавлен успешно");
    } else {
        showError("Ошибка: " . $conn->error);
    }
}

// 16. Поиск курса по названию
if (isset($_POST['search_course'])) {
    $name = $_POST['course_name'];

    $sql = "SELECT * FROM courses WHERE name LIKE '%$name%'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table><tr><th>ID</th><th>Название Курса</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["id"]. "</td><td>" . $row["name"]. "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 результатов";
    }
}

// 17. Удаление курса
if (isset($_POST['delete_course'])) {
    $course_id = $_POST['course_id'];

    $sql = "DELETE FROM courses WHERE id = $course_id";
    if ($conn->query($sql) === TRUE) {
        showSuccess("Курс удален успешно");
    } else {
        showError("Ошибка: " . $conn->error);
    }
}

// 18. Фильтрация студентов по группе
if (isset($_POST['filter_students_by_group'])) {
    $group_id = $_POST['group_id'];

    $sql = "SELECT * FROM students WHERE group_id = $group_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table><tr><th>ID</th><th>Имя</th><th>Группа</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["id"]. "</td><td>" . $row["name"]. "</td><td>" . $row["group_id"]. "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 результатов";
    }
}

// 19. Вывод студентов, зарегистрированных на несколько курсов
if (isset($_GET['action']) && $_GET['action'] == 'list_students_with_multiple_courses') {
    $sql = "SELECT students.name AS student_name, COUNT(student_courses.course_id) AS course_count 
            FROM students 
            JOIN student_courses ON students.id = student_courses.student_id 
            GROUP BY students.id 
            HAVING course_count > 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table><tr><th>Имя Студента</th><th>Количество Курсов</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["student_name"]. "</td><td>" . $row["course_count"]. "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 результатов";
    }
}

// 20. Вывод преподавателей с количеством их студентов
if (isset($_GET['action']) && $_GET['action'] == 'list_teachers_with_students') {
    $sql = "SELECT teachers.name AS teacher_name, COUNT(student_courses.student_id) AS total_students 
            FROM teachers 
            JOIN courses ON teachers.id = courses.teacher_id 
            JOIN student_courses ON courses.id = student_courses.course_id 
            GROUP BY teachers.id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table><tr><th>Имя Преподавателя</th><th>Количество Студентов</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["teacher_name"]. "</td><td>" . $row["total_students"]. "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 результатов";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление базой данных</title>
</head>
<body>

<h1>Управление базой данных</h1>

<!-- 1. Подключение к базе данных -->
<a href="?action=connect">Подключиться к базе данных</a>

<!-- 2. Добавление нового студента -->
<form action="" method="post">
    <h2>Добавить нового студента</h2>
    <label for="student_name">Имя:</label>
    <input type="text" id="student_name" name="student_name"><br><br>
    <label for="group_id">Группа:</label>
    <input type="number" id="group_id" name="group_id"><br><br>
    <input type="submit" name="add_student" value="Добавить">
</form>

<!-- 3. Вывод списка студентов -->
<a href="?action=list_students">Вывести список студентов</a>

<!-- 4. Добавление группы -->
<form action="" method="post">
    <h2>Добавить новую группу</h2>
    <label for="group_name">Название группы:</label>
    <input type="text" id="group_name" name="group_name"><br><br>
    <input type="submit" name="add_group" value="Добавить">
</form>

<!-- 5. Привязка студента к группе -->
<form action="" method="post">
    <h2>Привязать студента к группе</h2>
    <label for="student_id">ID Студента:</label>
    <input type="number" id="student_id" name="student_id"><br><br>
    <label for="group_id">ID Группы:</label>
    <input type="number" id="group_id" name="group_id"><br><br>
    <input type="submit" name="assign_group" value="Привязать">
</form>

<!-- 6. Вывод студентов с их группами -->
<a href="?action=list_students_with_groups">Вывести студентов с их группами</a>

<!-- 7. Регистрация студента на курс -->
<form action="" method="post">
    <h2>Зарегистрировать студента на курс</h2>
    <label for="student_id">ID Студента:</label>
    <input type="number" id="student_id" name="student_id"><br><br>
    <label for="course_id">ID Курса:</label>
    <input type="number" id="course_id" name="course_id"><br><br>
    <input type="submit" name="register_course" value="Зарегистрировать">
</form>

<!-- 8. Вывод всех курсов с количеством студентов -->
<a href="?action=list_courses_with_students">Вывести все курсы с количеством студентов</a>

<!-- 9. Удаление студента -->
<form action="" method="post">
    <h2>Удалить студента</h2>
    <label for="student_id">ID Студента:</label>

    <!-- 10. Обновление имени студента -->
<form action="" method="post">
    <h2>Обновить имя студента</h2>
    <label for="student_id">ID Студента:</label>
    <input type="number" id="student_id" name="student_id"><br><br>
    <label for="name">Новое имя:</label>
    <input type="text" id="name" name="name"><br><br>
    <input type="submit" name="update_student_name" value="Обновить">
</form>

<!-- 11. Вывод преподавателей и их курсов -->
<a href="?action=list_teachers_with_courses">Вывести преподавателей с их курсами</a>

<!-- 12. Поиск студента по имени -->
<form action="" method="post">
    <h2>Поиск студента по имени</h2>
    <label for="name">Имя студента:</label>
    <input type="text" id="name" name="name"><br><br>
    <input type="submit" name="search_student" value="Поиск">
</form>

<!-- 13. Вывод студентов без группы -->
<a href="?action=list_students_without_group">Вывести студентов без группы</a>

<!-- 14. Добавление нового курса -->
<form action="" method="post">
    <h2>Добавить новый курс</h2>
    <label for="course_name">Название курса:</label>
    <input type="text" id="course_name" name="course_name"><br><br>
    <label for="teacher_id">ID Преподавателя:</label>
    <input type="number" id="teacher_id" name="teacher_id"><br><br>
    <input type="submit" name="add_course" value="Добавить">
</form>

<!-- 15. Регистрация нового преподавателя -->
<form action="" method="post">
    <h2>Добавить нового преподавателя</h2>
    <label for="teacher_name">Имя преподавателя:</label>
    <input type="text" id="teacher_name" name="teacher_name"><br><br>
    <input type="submit" name="add_teacher" value="Добавить">
</form>

<!-- 16. Поиск курса по названию -->
<form action="" method="post">
    <h2>Поиск курса по названию</h2>
    <label for="course_name">Название курса:</label>
    <input type="text" id="course_name" name="course_name"><br><br>
    <input type="submit" name="search_course" value="Поиск">
</form>

<!-- 17. Удаление курса -->
<form action="" method="post">
    <h2>Удалить курс</h2>
    <label for="course_id">ID Курса:</label>
    <input type="number" id="course_id" name="course_id"><br><br>
    <input type="submit" name="delete_course" value="Удалить">
</form>

<!-- 18. Фильтрация студентов по группе -->
<form action="" method="post">
    <h2>Фильтровать студентов по группе</h2>
    <label for="group_id">ID Группы:</label>
    <input type="number" id="group_id" name="group_id"><br><br>
    <input type="submit" name="filter_students_by_group" value="Фильтровать">
</form>

<!-- 19. Вывод студентов, зарегистрированных на несколько курсов -->
<a href="?action=list_students_with_multiple_courses">Вывести студентов, зарегистрированных на несколько курсов</a>

<!-- 20. Вывод преподавателей с количеством их студентов -->
<a href="?action=list_teachers_with_students">Вывести преподавателей с количеством их студентов</a>

</body>
</html>
    
