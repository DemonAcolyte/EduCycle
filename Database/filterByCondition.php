<?php
$conditions = json_decode($_POST['conditions'], true);
$category = $_POST['category'];

if (!empty($conditions) && !empty($category)) {
  $conditionQuery = "WHERE book_condition IN (";
  $conditionQuery .= implode(',', array_fill(0, count($conditions), '?'));
  $conditionQuery .= ") AND category = '$category'";

  $stmt = $conn->prepare("SELECT * FROM books $conditionQuery");
  $stmt->bind_param(str_repeat('s', count($conditions)), ...$conditions);
  $stmt->execute();
  $result = $stmt->get_result();
} elseif (!empty($conditions)) {
  $conditionQuery = "WHERE book_condition IN (";
  $conditionQuery .= implode(',', array_fill(0, count($conditions), '?'));
  $conditionQuery .= ")";

  $stmt = $conn->prepare("SELECT * FROM books $conditionQuery");
  $stmt->bind_param(str_repeat('s', count($conditions)), ...$conditions);
  $stmt->execute();
  $result = $stmt->get_result();
} elseif (!empty($category)) {
  $stmt = $conn->prepare("SELECT * FROM books WHERE category = '$category'");
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  $result = $conn->query("SELECT * FROM books");
}