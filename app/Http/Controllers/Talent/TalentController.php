<?php

namespace App\Http\Controllers\Talent;

use App\JobCategories;
use File;
use ImgUploader;
use Auth;
use DB;
use Input;
use Carbon\Carbon;
use Redirect;
use App\User;
use App\Gender;
use App\MaritalStatus;
use App\Country;
use App\State;
use App\City;
use App\JobExperience;
use App\JobSkill;
use App\CareerLevel;
use App\Industry;
use App\FunctionalArea;
use App\ProfileSummary;
use App\ProfileProject;
use App\ProfileExperience;
use App\ProfileEducation;
use App\ProfileSkill;
use App\ProfileLanguage;
use App\Package;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DataTables;
use App\Http\Requests\UserFormRequest;
use App\Http\Requests\ProfileProjectFormRequest;
use App\Http\Requests\ProfileProjectImageFormRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Traits\CommonUserFunctions;
use App\Traits\ProfileSummaryTrait;
use App\Traits\ProfileCvsTrait;
use App\Traits\ProfileProjectsTrait;
use App\Traits\ProfileExperienceTrait;
use App\Traits\ProfileEducationTrait;
use App\Traits\ProfileSkillTrait;
use App\Traits\ProfileLanguageTrait;
use App\Traits\Skills;
use App\Traits\JobSeekerPackageTrait;
use App\Helpers\DataArrayHelper;
use Illuminate\Database\Eloquent\Collection;
use App\Traits\FetchJobs;
use App\Http\Controllers\Talent\Schema;

class TalentController extends Controller
{

    use CommonUserFunctions;
    use ProfileSummaryTrait;
    use ProfileCvsTrait;
    use ProfileProjectsTrait;
    use ProfileExperienceTrait;
    use ProfileEducationTrait;
    use ProfileSkillTrait;
    use ProfileLanguageTrait;
    use Skills;
    use JobSeekerPackageTrait;
    use FetchJobs;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


     public function indexTalents(Request $request){
        $job_skills = JobSkill::where('lang', 'en')->get();
        $filters = $request->get('s', array());
        $categories = JobCategories::with('talent_list')->get();

        $users = User::where('verified','1')
                        ->where('is_active','1')
                        ->with('profileSummary')
                        ->with('profileSkill')
                        ->with('jobSkill')
                        ->paginate(9);
        foreach($users as $user){ 
            foreach($filters as $filter){   
                $skill_ids = JobSkill::where('job_skill', $filter)->where('lang', 'en')->get('job_skill_id');
                foreach($skill_ids as $skill_id){
                    $filteredUsers = User:: where('verified','1')
                        ->where('is_active','1')
                        ->with('profileSummary')
                        ->with('profileSkill')
                        ->with('jobSkill')
                        ->wherehas('profileSkill', function($query) use ($skill_id){
                             $query->where('profile_skills.job_skill_id', $skill_id->job_skill_id);
                            //  ->groupBy('user_id');
                            })
                        // ->wherehas('profileSkill', function($query) use ($filter){
                        //     $query->where('profile_skills.job_skill_id', $filter)
                        //           ->distinct('user_id');
                        //     }) 
                        // ->groupBy('profile_skills.user_id ')
                        ->paginate(9);  
                        // dd($filteredUsers);
                    // dd($filteredUsers);
                }
            }   
            
        }
        
        if(isset($filteredUsers)){
            return view('talent.list')->with('users', $users)
                                  ->with('job_skills', $job_skills)
                                  ->with('filters', $filters)
                                  ->with('filteredUsers', $filteredUsers)
                                  ->with('categories', $categories);
        }
        return view('talent.list')->with('users', $users)
                                  ->with('job_skills', $job_skills)
                                  ->with('filters', $filters)
                                  ->with('categories', $categories);
    }

    // public function indexTalents(Request $request){
    //     $job_skills = JobSkill::where('lang', 'en')->get();
    //     $filters = $request->get('job_skill_id', array());
    //     $categories = JobCategories::with('talent_list')->get();

    //     $users = User::where('verified','1')
    //                     ->where('is_active','1')
    //                     ->with('profileSummary')
    //                     ->with('profileSkill')
    //                     ->with('jobSkill')
    //                     ->paginate(9);
    //     foreach($users as $user){
    //         foreach($filters as $filter){
    //             $filteredUsers = User::where('verified','1')
    //                     ->where('is_active','1')
    //                     ->with('profileSummary')
    //                     ->with('profileSkill')
    //                     ->with('jobSkill')
    //                     ->wherehas('jobSkill', function($query) use ($filter){
    //                          $query->where('job_skills.job_skill_id', $filter);
    //                         })
    //                     ->paginate(9);
    //         }   
            
    //     }
        
    //     if(isset($filteredUsers)){
    //         return view('talent.list')->with('users', $users)
    //                               ->with('job_skills', $job_skills)
    //                               ->with('filters', $filters)
    //                               ->with('filteredUsers', $filteredUsers)
    //                               ->with('categories', $categories);
    //     }
    //     return view('talent.list')->with('users', $users)
    //                               ->with('job_skills', $job_skills)
    //                               ->with('filters', $filters)
    //                               ->with('categories', $categories);
    // }


   public function limitDes($strLimit)
    {
        return Str::limit($strLimit, 100 );
    }
	
    public function talentResume($slug)
    {	
	echo "<pre>";
		echo $users = User::select('*')->get($slug);
	echo "<pre>";					   
         return view('talent.talent-resume')->with('users', $users); 
    }
	
    public function talents($slug)
    {   
        
    
       $job_skills = JobSkill::where('job_skill',$slug)->limit(1)->get()->first();
	   
	   if (isset($job_skills)){

			$profile_skills = ProfileSkill::select('*')
				->join('users','profile_skills.user_id','=','users.id')
				->join('profile_summaries','profile_skills.user_id','=','profile_summaries.user_id')
				->where('profile_skills.job_skill_id',$job_skills->job_skill_id)
				->get(); 
		  
			return view('talent.talent-categorylist')->with('profile_skills',$profile_skills)->with("job_skills",$job_skills);
	   
	   }else{
		   return view('errors.404');
	   }
  
    }
    public function createUser()
    {
        $genders = DataArrayHelper::defaultGendersArray();
        $maritalStatuses = DataArrayHelper::defaultMaritalStatusesArray();
        $nationalities = DataArrayHelper::defaultNationalitiesArray();
        $countries = DataArrayHelper::defaultCountriesArray();
        $jobExperiences = DataArrayHelper::defaultJobExperiencesArray();
        $careerLevels = DataArrayHelper::defaultCareerLevelsArray();
        $industries = DataArrayHelper::defaultIndustriesArray();
        $functionalAreas = DataArrayHelper::defaultFunctionalAreasArray();
        $packages = Package::select('id', DB::raw("CONCAT(`package_title`, ', $', `package_price`, ', Days:', `package_num_days`, ', Listings:', `package_num_listings`) AS package_detail"))->where('package_for', 'like', 'job_seeker')->pluck('package_detail', 'id')->toArray();
        $upload_max_filesize = UploadedFile::getMaxFilesize() / (1048576);

        return view('admin.user.add')
                        ->with('genders', $genders)
                        ->with('maritalStatuses', $maritalStatuses)
                        ->with('nationalities', $nationalities)
                        ->with('countries', $countries)
                        ->with('jobExperiences', $jobExperiences)
                        ->with('careerLevels', $careerLevels)
                        ->with('industries', $industries)
                        ->with('functionalAreas', $functionalAreas)
                        ->with('upload_max_filesize', $upload_max_filesize)
                        ->with('packages', $packages);
    }

    public function storeUser(UserFormRequest $request)
    {
        $user = new User();
        /*         * **************************************** */
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = ImgUploader::UploadImage('user_images', $image, $request->input('name'), 300, 300, false);
            $user->image = $fileName;
        }
        /*         * ************************************** */
        $user->first_name = $request->input('first_name');
        $user->middle_name = $request->input('middle_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        if (!empty($request->input('password'))) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->father_name = $request->input('father_name');
        $user->date_of_birth = $request->input('date_of_birth');
        $user->gender_id = $request->input('gender_id');
        $user->marital_status_id = $request->input('marital_status_id');
        $user->nationality_id = $request->input('nationality_id');
        $user->national_id_card_number = $request->input('national_id_card_number');
        $user->country_id = $request->input('country_id');
        $user->state_id = $request->input('state_id');
        $user->city_id = $request->input('city_id');
        $user->phone = $request->input('phone');
        $user->mobile_num = $request->input('mobile_num');
        $user->job_experience_id = $request->input('job_experience_id');
        $user->career_level_id = $request->input('career_level_id');
        $user->industry_id = $request->input('industry_id');
        $user->functional_area_id = $request->input('functional_area_id');
        $user->current_salary = $request->input('current_salary');
        $user->expected_salary = $request->input('expected_salary');
        $user->salary_currency = $request->input('salary_currency');
        $user->street_address = $request->input('street_address');
        $user->is_immediate_available = $request->input('is_immediate_available');
        $user->is_active = $request->input('is_active');
        $user->verified = $request->input('verified');
        $user->save();

        /*         * *********************** */
        $user->name = $user->getName();
        $user->update();
        $this->updateUserFullTextSearch($user);
        /*         * *********************** */
        /*         * ************************************ */
        if ($request->has('job_seeker_package_id') && $request->input('job_seeker_package_id') > 0) {
            $package_id = $request->input('job_seeker_package_id');
            $package = Package::find($package_id);
            $this->addJobSeekerPackage($user, $package);
        }
        /*         * ************************************ */

        flash('User has been added!')->success();
        return \Redirect::route('edit.user', array($user->id));
    }

    public function editUser($id)
    {
        $genders = DataArrayHelper::defaultGendersArray();
        $maritalStatuses = DataArrayHelper::defaultMaritalStatusesArray();
        $nationalities = DataArrayHelper::defaultNationalitiesArray();
        $countries = DataArrayHelper::defaultCountriesArray();
        $jobExperiences = DataArrayHelper::defaultJobExperiencesArray();
        $careerLevels = DataArrayHelper::defaultCareerLevelsArray();
        $industries = DataArrayHelper::defaultIndustriesArray();
        $functionalAreas = DataArrayHelper::defaultFunctionalAreasArray();

        $upload_max_filesize = UploadedFile::getMaxFilesize() / (1048576);
        $user = User::findOrFail($id);
        if ($user->package_id > 0) {
            $package = Package::find($user->package_id);
            $packages = Package::select('id', DB::raw("CONCAT(`package_title`, ', $', `package_price`, ', Days:', `package_num_days`, ', Listings:', `package_num_listings`) AS package_detail"))->where('package_for', 'like', 'job_seeker')->where('id', '<>', $user->package_id)->where('package_price', '>=', $package->package_price)->pluck('package_detail', 'id')->toArray();
        } else {
            $packages = Package::select('id', DB::raw("CONCAT(`package_title`, ', $', `package_price`, ', Days:', `package_num_days`, ', Listings:', `package_num_listings`) AS package_detail"))->where('package_for', 'like', 'job_seeker')->pluck('package_detail', 'id')->toArray();
        }

        return view('admin.user.edit')
                        ->with('genders', $genders)
                        ->with('maritalStatuses', $maritalStatuses)
                        ->with('nationalities', $nationalities)
                        ->with('countries', $countries)
                        ->with('jobExperiences', $jobExperiences)
                        ->with('careerLevels', $careerLevels)
                        ->with('industries', $industries)
                        ->with('functionalAreas', $functionalAreas)
                        ->with('user', $user)
                        ->with('upload_max_filesize', $upload_max_filesize)
                        ->with('packages', $packages);
    }

    public function updateUser($id, UserFormRequest $request)
    {
        $user = User::findOrFail($id);
        /*         * **************************************** */
        if ($request->hasFile('image')) {
            $is_deleted = $this->deleteUserImage($user->id);
            $image = $request->file('image');
            $fileName = ImgUploader::UploadImage('user_images', $image, $request->input('name'), 300, 300, false);
            $user->image = $fileName;
        }
		
		if ($request->hasFile('cover_image')) {
			$is_deleted = $this->deleteUserCoverImage($user->id);
            $cover_image = $request->file('cover_image');
            $fileName_cover_image = ImgUploader::UploadImage('user_images', $cover_image, $request->input('name'), 1140, 250, false);
            $user->cover_image = $fileName_cover_image;
        }
		
        /*         * ************************************** */
        $user->first_name = $request->input('first_name');
        $user->middle_name = $request->input('middle_name');
        $user->last_name = $request->input('last_name');
        /*         * *********************** */
        $user->name = $user->getName();
        /*         * *********************** */
        $user->email = $request->input('email');
        if (!empty($request->input('password'))) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->father_name = $request->input('father_name');
        $user->date_of_birth = $request->input('date_of_birth');
        $user->gender_id = $request->input('gender_id');
        $user->marital_status_id = $request->input('marital_status_id');
        $user->nationality_id = $request->input('nationality_id');
        $user->national_id_card_number = $request->input('national_id_card_number');
        $user->country_id = $request->input('country_id');
        $user->state_id = $request->input('state_id');
        $user->city_id = $request->input('city_id');
        $user->phone = $request->input('phone');
        $user->mobile_num = $request->input('mobile_num');
        $user->job_experience_id = $request->input('job_experience_id');
        $user->career_level_id = $request->input('career_level_id');
        $user->industry_id = $request->input('industry_id');
        $user->functional_area_id = $request->input('functional_area_id');
        $user->current_salary = $request->input('current_salary');
        $user->expected_salary = $request->input('expected_salary');
        $user->salary_currency = $request->input('salary_currency');
        $user->street_address = $request->input('street_address');
        $user->is_immediate_available = $request->input('is_immediate_available');
        $user->is_active = $request->input('is_active');
        $user->verified = $request->input('verified');
        $user->update();

        $this->updateUserFullTextSearch($user);
        /*         * ************************************ */
        if ($request->has('job_seeker_package_id') && $request->input('job_seeker_package_id') > 0) {
            $package_id = $request->input('job_seeker_package_id');
            $package = Package::find($package_id);
            if ($user->package_id > 0) {
                $this->updateJobSeekerPackage($user, $package);
            } else {
                $this->addJobSeekerPackage($user, $package);
            }
        }
        /*         * ************************************ */

        flash('User has been updated!')->success();
        return \Redirect::route('edit.user', array($user->id));
    }

    public function fetchUsersData(Request $request)
    {
        $users = User::select(
                        [
                            'users.id',
                            'users.first_name',
                            'users.middle_name',
                            'users.last_name',
                            'users.email',
                            'users.password',
                            'users.phone',
                            'users.country_id',
                            'users.state_id',
                            'users.city_id',
                            'users.is_immediate_available',
                            'users.num_profile_views',
                            'users.is_active',
                            'users.verified',
                            'users.created_at',
                            'users.updated_at'
        ]);
        return Datatables::of($users)
                        ->filter(function ($query) use ($request) {
                            if ($request->has('id') && !empty($request->id)) {
                                $query->where('users.id', 'like', "{$request->get('id')}");
                            }
                            if ($request->has('name') && !empty($request->name)) {
                                $query->where(function($q) use ($request) {
                                    $q->where('users.first_name', 'like', "%{$request->get('name')}%")
                                    ->orWhere('users.middle_name', 'like', "%{$request->get('name')}%")
                                    ->orWhere('users.last_name', 'like', "%{$request->get('name')}%");
                                });
                            }
                            if ($request->has('email') && !empty($request->email)) {
                                $query->where('users.email', 'like', "%{$request->get('email')}%");
                            }
                        })
                        ->addColumn('name', function ($users) {
                            return $users->first_name . ' ' . $users->middle_name . ' ' . $users->last_name;
                        })
                        ->addColumn('action', function ($users) {
                            /*                             * ************************* */
                            $active_txt = 'Make Active';
                            $active_href = 'make_active(' . $users->id . ');';
                            $active_icon = 'square-o';
                            if ((int) $users->is_active == 1) {
                                $active_txt = 'Make InActive';
                                $active_href = 'make_not_active(' . $users->id . ');';
                                $active_icon = 'check-square-o';
                            }
                            /*                             * ************************* */
                            /*                             * ************************* */
                            $verified_txt = 'Not Verified';
                            $verified_href = 'make_verified(' . $users->id . ');';
                            $verified_icon = 'square-o';
                            if ((int) $users->verified == 1) {
                                $verified_txt = 'Verified';
                                $verified_href = 'make_not_verified(' . $users->id . ');';
                                $verified_icon = 'check-square-o';
                            }
                            /*                             * ************************* */
                            return '
				<div class="btn-group">
					<button class="btn blue dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Action
						<i class="fa fa-angle-down"></i>
					</button>
					<ul class="dropdown-menu">
						<li>
							<a href="' . route('edit.user', ['id' => $users->id]) . '"><i class="fa fa-pencil" aria-hidden="true"></i>Edit</a>
						</li>						
						<li>
							<a href="javascript:void(0);" onclick="delete_user(' . $users->id . ');" class=""><i class="fa fa-trash-o" aria-hidden="true"></i>Delete</a>
						</li>
						<li>
						<a href="javascript:void(0);" onClick="' . $active_href . '" id="onclick_active_' . $users->id . '"><i class="fa fa-' . $active_icon . '" aria-hidden="true"></i>' . $active_txt . '</a>
						</li>
						<li>
						<a href="javascript:void(0);" onClick="' . $verified_href . '" id="onclick_verified_' . $users->id . '"><i class="fa fa-' . $verified_icon . '" aria-hidden="true"></i>' . $verified_txt . '</a>
						</li>																																							
					</ul>
				</div>';
                        })
                        ->rawColumns(['action', 'name'])
                        ->setRowId(function($users) {
                            return 'user_dt_row_' . $users->id;
                        })
                        ->make(true);
    }

    public function makeActiveUser(Request $request)
    {
        $id = $request->input('id');
        try {
            $user = User::findOrFail($id);
            $user->is_active = 1;
            $user->update();
            echo 'ok';
        } catch (ModelNotFoundException $e) {
            echo 'notok';
        }
    }

    public function makeNotActiveUser(Request $request)
    {
        $id = $request->input('id');
        try {
            $user = User::findOrFail($id);
            $user->is_active = 0;
            $user->update();
            echo 'ok';
        } catch (ModelNotFoundException $e) {
            echo 'notok';
        }
    }

    public function makeVerifiedUser(Request $request)
    {
        $id = $request->input('id');
        try {
            $user = User::findOrFail($id);
            $user->verified = 1;
            $user->update();
            echo 'ok';
        } catch (ModelNotFoundException $e) {
            echo 'notok';
        }
    }

    public function makeNotVerifiedUser(Request $request)
    {
        $id = $request->input('id');
        try {
            $user = User::findOrFail($id);
            $user->verified = 0;
            $user->update();
            echo 'ok';
        } catch (ModelNotFoundException $e) {
            echo 'notok';
        }
    }

    /*     * ******************************************** */
}