<?php

use Illuminate\Http\Request;

Route::group(['middleware'=>['auth:api','api.verify']],function(){

    // Practitioner Reference
    Route::POST('/satusehat/pract', 'SearchPractitionerController@createToken');
    Route::GET('/satusehat/pract/{nik}', 'SearchPractitionerController@searchNIK');
    Route::GET('/satusehat/pract', 'SearchPractitionerController@searchName');

    // Patient Reference
    // Route::POST('/satusehat/patient', 'SearchPatientController@createToken');
    // Route::GET('/satusehat/patient/{id}/nik', 'SearchPatientController@searchNIK');
    // Route::GET('/satusehat/patient', 'SearchPatientController@searchName');
    // Route::GET('/satusehat/patient/{id}/id', 'SearchPatientController@searchID');

    // Organization
    Route::GET('/satusehat/organization', 'CreateOrganizationController@lists');
    Route::POST('/satusehat/organization', 'CreateOrganizationController@createOrganization');
    Route::GET('/satusehat/organization/{id}/id', 'CreateOrganizationController@searchID');

});

Route::POST('/satusehat/patient', 'SearchPatientController@createToken');
Route::GET('/satusehat/patient/{id}/nik', 'SearchPatientController@searchNIK');
Route::GET('/satusehat/patient', 'SearchPatientController@searchName');
Route::GET('/satusehat/patient/{id}/id', 'SearchPatientController@searchID');
Route::POST('/satusehat/createPatient', 'CreatePatientController@create');

// Location
Route::GET('/satusehat/location', 'CreateLocationController@lists');
Route::POST('/satusehat/location', 'CreateLocationController@createLocation');
Route::GET('/satusehat/location/{id}/id', 'CreateLocationController@searchID');
Route::GET('/satusehat/location/{id}/identifier', 'CreateLocationController@searchIdentifier');
Route::GET('/satusehat/location/{keyword}/organization', 'CreateLocationController@searchOrgID');
Route::GET('/satusehat/location/{keyword}/name', 'CreateLocationController@searchName');
Route::PUT('/satusehat/location/{id}', 'CreateLocationController@updateLocation');
Route::PATCH('/satusehat/location/{id}/inactive', 'CreateLocationController@deleteLocation');

// Encounter
Route::GET('/satusehat/encounter', 'EncounterController@lists');
Route::POST('/satusehat/encounter', 'EncounterController@createEncounter');
Route::GET('/satusehat/encounter/{id}/id', 'EncounterController@searchID');
Route::GET('/satusehat/encounter/{keyword}/subject', 'EncounterController@searchSubject');
Route::PUT('/satusehat/encounter/{id}/{status}', 'EncounterController@updateEncounter');
Route::PATCH('/satusehat/encounter/{id}/inactive', 'EncounterController@deleteEncounter');

// Allergy Tolerance
Route::GET('/satusehat/allergy', 'AllergyToleranceController@lists');
Route::POST('/satusehat/allergy', 'AllergyToleranceController@createAllergy');
Route::GET('/satusehat/allergy/{id}/id', 'AllergyToleranceController@searchID');
Route::GET('/satusehat/allergy/subject', 'AllergyToleranceController@searchSubject');
Route::PUT('/satusehat/allergy/{id}', 'AllergyToleranceController@updateAllergy');
// Tidak ada kolom menandakan Allergy Tolerance bisa dihapus atau dinonaktifkan
// Sehingga menunggakan contoh verificationStatus = Refuted
Route::PATCH('/satusehat/allergy/{id}/refuted', 'AllergyToleranceController@deleteAllergy');

// Vital Sign
// Route::GET('/satusehat/vital_sign', 'VitalSignController@lists');
// Route::POST('/satusehat/vital_sign', 'VitalSignController@createVitalSign');
// Route::GET('/satusehat/vital_sign/{id}/id', 'VitalSignController@searchID');
// Route::GET('/satusehat/vital_sign/subject', 'VitalSignController@searchSubject');
// Route::PUT('/satusehat/vital_sign/{id}', 'VitalSignController@updateVitalSign');
// // Tidak ada kolom menandakan Vital Sign bisa dihapus atau dinonaktifkan
// // Sehingga diasumsikan mengunnakan kolom valueQuantity sebagai contoh
// Route::PATCH('/satusehat/vital_sign/{id}/valueQuantity', 'VitalSignController@deleteVitalSign');


// Level of responsiveness
Route::GET('/satusehat/responsiveness', 'ResponsivenessController@lists');
Route::POST('/satusehat/responsiveness', 'ResponsivenessController@createResponsiveness');
Route::GET('/satusehat/responsiveness/{id}/id', 'ResponsivenessController@searchID');
Route::GET('/satusehat/responsiveness/subject', 'ResponsivenessController@searchSubject');
Route::PUT('/satusehat/responsiveness/{id}', 'ResponsivenessController@updateResponsiveness');
// Tidak ada kolom menandakan Vital Sign bisa dihapus atau dinonaktifkan
// Sehingga diasumsikan mengunnakan kolom valueQuantity sebagai contoh
Route::PATCH('/satusehat/responsiveness/{id}/valueCodeableConcept', 'ResponsivenessController@deleteResponsiveness');


// Vital Sign
Route::GET('/satusehat/observation', 'ObservationController@lists');
Route::POST('/satusehat/observation/vital_sign', 'ObservationController@createObsVitalSign');
Route::POST('/satusehat/observation/lab', 'ObservationController@createObsLab');
Route::GET('/satusehat/observation/{id}/id', 'ObservationController@searchID');
Route::GET('/satusehat/observation/subject', 'ObservationController@searchSubject');
Route::PUT('/satusehat/observation/vital_sign/{id}', 'ObservationController@updateVitalSign');
// Tidak ada kolom menandakan Vital Sign bisa dihapus atau dinonaktifkan
// Sehingga diasumsikan mengunnakan kolom valueQuantity sebagai contoh
Route::PATCH('/satusehat/observation/vital_sign/{id}/valueQuantity', 'ObservationController@deleteVitalSign');

// Level of responsiveness
Route::GET('/satusehat/responsiveness', 'ResponsivenessController@lists');
Route::POST('/satusehat/responsiveness', 'ResponsivenessController@createResponsiveness');
Route::GET('/satusehat/responsiveness/{id}/id', 'ResponsivenessController@searchID');
Route::GET('/satusehat/responsiveness/subject', 'ResponsivenessController@searchSubject');
Route::PUT('/satusehat/responsiveness/{id}', 'ResponsivenessController@updateResponsiveness');
// Tidak ada kolom menandakan Vital Sign bisa dihapus atau dinonaktifkan
// Sehingga diasumsikan mengunnakan kolom valueQuantity sebagai contoh
Route::PATCH('/satusehat/responsiveness/{id}/valueCodeableConcept', 'ResponsivenessController@deleteResponsiveness');

// Service Request
Route::GET('/satusehat/service_request', 'ServiceRequestController@lists');
Route::POST('/satusehat/service_request/lab', 'ServiceRequestController@createServiceRequestLab');
Route::POST('/satusehat/service_request/control', 'ServiceRequestController@createServiceRequestControl');
Route::GET('/satusehat/service_request/{id}/id', 'ServiceRequestController@searchID');
Route::GET('/satusehat/service_request/subject', 'ServiceRequestController@searchSubject');
Route::PUT('/satusehat/service_request/{id}', 'ServiceRequestController@updateServiceRequest');
Route::PATCH('/satusehat/service_request/{id}/revoked', 'ServiceRequestController@deleteServiceRequest');

// Specimen
Route::GET('/satusehat/specimen', 'SpecimenController@lists');
Route::POST('/satusehat/specimen', 'SpecimenController@createSpecimen');
Route::GET('/satusehat/specimen/{id}/id', 'SpecimenController@searchID');
Route::GET('/satusehat/specimen/subject', 'SpecimenController@searchSubject');
Route::PUT('/satusehat/specimen/{id}', 'SpecimenController@updateSpecimen');
Route::PATCH('/satusehat/specimen/{id}/unavailable', 'SpecimenController@deleteSpecimen');

// Diagnostic Report
Route::GET('/satusehat/report/diagnostic', 'DiagnosticReportController@lists');
Route::POST('/satusehat/report/diagnostic', 'DiagnosticReportController@createDiagnosticReport');
Route::GET('/satusehat/report/diagnostic/{id}/id', 'DiagnosticReportController@searchID');
Route::GET('/satusehat/report/diagnostic/subject', 'DiagnosticReportController@searchSubject');
Route::PUT('/satusehat/report/diagnostic/{id}', 'DiagnosticReportController@updateDiagnosticReport');
Route::PATCH('/satusehat/report/diagnostic/{id}/cancelled', 'DiagnosticReportController@deleteDiagnosticReport');

// Procedure
Route::GET('/satusehat/procedure', 'ProcedureController@lists');
Route::POST('/satusehat/procedure', 'ProcedureController@createProcedure');
Route::GET('/satusehat/procedure/{id}/id', 'ProcedureController@searchID');
Route::GET('/satusehat/procedure/subject', 'ProcedureController@searchSubject');
Route::PUT('/satusehat/procedure/{id}', 'ProcedureController@updateProcedure');
Route::PATCH('/satusehat/procedure/{id}/not_done', 'ProcedureController@deleteProcedure');

// Medication : Membuat Resep
// Medication Request : Pengiriman Reep ke Apotik / Farmasi
// Medication Dispense : Pengeluaran Obat dari Apotik / Farmasi

// Condition

// Composition : Pengiriman Data Diet

// Pengiriman Data Prognosis

// Pengiriman Data Kondisi Saat Meninggalkan Rumah Sakit
