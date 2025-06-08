-- phpMyAdmin SQL Dump
-- Expense Management System Database Structure

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Database: `expense_management`

-- Table structure for table `tbluser`
CREATE TABLE IF NOT EXISTS `tbluser` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `FullName` varchar(150) NOT NULL,
  `Email` varchar(200) NOT NULL UNIQUE,
  `MobileNumber` varchar(15) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `ProfilePicture` varchar(255) DEFAULT NULL,
  `RegDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `tblexpensecategory`
CREATE TABLE IF NOT EXISTS `tblexpensecategory` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `CategoryName` varchar(200) NOT NULL,
  `CreationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `tblexpense`
CREATE TABLE IF NOT EXISTS `tblexpense` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `UserId` int(10) NOT NULL,
  `ExpenseDate` date NOT NULL,
  `ExpenseItem` varchar(200) NOT NULL,
  `ExpenseCost` decimal(10,2) NOT NULL,
  `CategoryId` int(10) NOT NULL,
  `NoteDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  FOREIGN KEY (`UserId`) REFERENCES `tbluser`(`ID`) ON DELETE CASCADE,
  FOREIGN KEY (`CategoryId`) REFERENCES `tblexpensecategory`(`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default expense categories
INSERT INTO `tblexpensecategory` (`CategoryName`) VALUES
('Household'),
('Food'),
('Transportation'),
('Entertainment'),
('Shopping'),
('Medical'),
('Utilities'),
('Others');

COMMIT; 