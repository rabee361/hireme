# Technical Implementation Plan (Updated)

## 1. Profile Edit APIs (Company, Student, Customer)
- **Form Requests**: 
  - Create `UpdateCompanyProfileRequest`, `UpdateStudentProfileRequest`, and `UpdateCustomerProfileRequest`.
  - Validate fields for both the `users` table and their respective `_profiles` tables.
- **Controllers**:
  - Add `update` method to `CompanyController`, `StudentController`, and `CustomerController`.
  - Use DB transactions to update both the `users` record and the `profile` record simultaneously.
- **Routes (`routes/api.php`)**:
  - `PUT /companies/{company}`
  - `PUT /students/{student}`
  - `PUT /customers/{customer}`

## 2. Company Saved Students API
- **Migration**: Create `create_company_saved_students_table` with `company_id` and `student_id`.
- **Models**: Add `savedStudents()` to `Company` and `savedByCompanies()` to `Student`.
- **Controller**: Create `SavedStudentController` (`index`, `store`, `destroy`).
- **Routes (`routes/api.php`)**:
  - `GET /saved-students`
  - `POST /saved-students/{student}`
  - `DELETE /saved-students/{student}`

## 3. Company "My Ads" API
- **Controller**: Add `myAds(Request $request)` to `AdController` to fetch ads for the logged-in company.
- **Routes**: `GET /my-ads`

---

## NEW: 4. Student Saved Ads API ("My Ads")
- **Migration**: Create `create_student_saved_ads_table` with `student_id` (foreign key to `users.id`) and `ad_id` (foreign key to `ads.id`).
- **Models**: Add `savedAds()` to `Student` and `savedByStudents()` to `Ad`.
- **Controller**: Create `StudentSavedAdController`.
  - `index()`: List ads saved by the logged-in student.
  - `store(Ad $ad)`: Add an ad to the student's saved list.
  - `destroy(Ad $ad)`: Remove an ad from the list.
- **Routes**:
  - `GET /student/saved-ads`
  - `POST /student/saved-ads/{ad}`
  - `DELETE /student/saved-ads/{ad}`

## NEW: 5. Application Logic (Ads & Projects)
*Validation note: The `AdApplication` and `ProjectApplication` models and database tables already exist, and they already include timestamps (`created_at`) for when the application was sent. However, the Controllers and Routes to interact with them are currently missing.*

- **Student Application Actions (Create, Update, Delete)**:
  - Create `AdApplicationController` and `ProjectApplicationController`.
  - Add `store` (apply), `update` (edit application details like resume/github), and `destroy` (delete application).
  - Add endpoints for a student to view their own applications (`GET /student/my-ad-applications` and `GET /student/my-project-applications`).
  - *Data mapping*: Ensure the `created_at` timestamp is returned in all application payloads.
- **Company / Customer Application Review**:
  - Add an API for Companies to list applications for a specific ad: `GET /ads/{ad}/applications` (handled in `AdApplicationController@index`).
  - Add an API for Customers to list applications for a specific project: `GET /projects/{project}/applications` (handled in `ProjectApplicationController@index`).
  - The payloads will return the application data + the `created_at` timestamp + the student's profile information.

## 6. Postman Collection Update
- Parse `hireme.postman_collection.json` and add all the new endpoints outlined in steps 1-5.
