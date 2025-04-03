# Employee Management Module User Guide  

## Table of Contents  
1. [Overview](#overview)  
2. [Employee Attributes](#employee-attributes)  
3. [Viewing and Managing Employees](#viewing-and-managing-employees)  
   - 3.1 [Employee Table Interface](#employee-table-interface)  
   - 3.2 [Adding an Employee](#adding-an-employee)  
   - 3.3 [Post-Creation Steps for Employees](#post-creation-steps-for-employees)  
4. [Permissions & Role-Based Access](#permissions--role-based-access)  
   - 4.1 [Managing Permissions](#managing-permissions)  
   - 4.2 [Default Access Levels by Role](#default-access-levels-by-role)  

---
<a name="overview"></a>
## Overview  
The **Employee Management Module** enables enterprises to efficiently manage employee records, permissions, and access controls. Key features include:  
- Storing comprehensive employee profiles.  
- Configuring role-based permissions.  
- Streamlining onboarding with automated credentials.  

---
<a name="employee-attributes"></a>
## Employee Attributes  
Employee profiles include the following fields, categorized for clarity:  

### **Personal Information**  
- First Name  
- Last Name  
- Date of Birth  
- Home District  
- Next of Kin  
- Passport Photo  
- National ID Photo  
- Phone Number  
- Email  

### **Employment Details**  
- Staff ID  
- Position ID  
- Department ID  
- Title  
- Job Description  
- Date of Entry  
- Contract Expiry Date  
- Contract Documents  
- Basic Salary  
- Entitled Leave Days  

### **Government & Tax Identifiers**  
- NIN (National Identification Number)  
- NSSF No (Social Security Number)  
- TIN No (Tax Identification Number)  

### **System Credentials**  
- User ID (auto-generated)  

---
<a name="viewing-and-managing-employees"></a>
## Viewing and Managing Employees  

### Employee Table Interface  
**Access Path**:  
1. Navigate to the **General** section in the sidebar.  
2. Click **Employees** (menu item will highlight in gray).  

**Table Features**:  
- **Filters**: Search by:  
  - Employee Name  
  - Position  
  - Department  
  - Contract Duration  
- **Actions**:  
  - Sort columns.  
  - Export data (if permissions allow).  

### Adding an Employee  
1. Click the **+ Add Employee** button (blue button at the top right).  
2. Fill in the employee details in the form.  
   - **Mandatory Field**: Email (used for system access credentials).  
3. Click **Create**.  
   - A success toast notification will appear.  
   - Login credentials (email + temporary password) are automatically sent to the employee.  

---

<a name="post-creation-steps-for-employees"></a>

### Post-Creation Steps for Employees  
**Employees must reset their password after first login**:  
1. Log in to UNCST HRMIS using the provided credentials.  
2. Click your **avatar** (top-right corner).  
3. Select **My Profile** from the dropdown.  
4. Under **Account Settings**:  
   - Update your password.  
   - Edit personal details (optional).  
5. Click **Create**.  

---
<a name="permissions--role-based-access"></a>

## Permissions & Role-Based Access  

### Managing Permissions  
**To configure permissions for roles**:  
1. Go to **Settings** > **Roles**.  
2. Click **Edit** next to the desired role.  
3. Toggle permissions (check/uncheck boxes):  
   - Delete employee records  
   - Edit employee details  
   - View full employee profiles  
4. Click **Update Role** to save changes.  

---
<a name="default-access-levels-by-role"></a>

### Default Access Levels by Role  
| Role                          | Access Scope                                  |  
|-------------------------------|----------------------------------------------|  
| **HR**                        | View/edit/delete all employees                      |  
| **Head of Division**          | View employees in their division only        |  
| **Executive Secretary**       | View all employees                           |  
| **Staff**                     | View only their own profile                  |  
| **Assistant Executive Secretary** | View all employees                     |  

---

## Important Notes  
- 📁 **Documents**: Upload contract copies and national IDs in PDF/JPEG/PNG format.  
- ⚠️ **Data Integrity**: Deleting an employee removes all associated records. Use with caution.  