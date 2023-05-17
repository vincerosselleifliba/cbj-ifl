<?php
Route::get('talents', 'Talent\TalentController@indexTalents')->name('talent');
Route::get('talents/{slug}', 'Talent\TalentController@talents');
// Route::get('talents/{slug}', 'Talent\TalentController@talents')->where('slug', '[a-zA-Z0-9\-_]+');

Route::get('talent/resume/{slug}', 'Talent\TalentController@talentResume');