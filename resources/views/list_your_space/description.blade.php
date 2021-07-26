<div class="row my-4 language-tabs-container" ng-init="current_tab_code='en'">
	<div class="col-12 col-md-8 description_heading">
		<ul class="description-tabs" id="multiple_description" role="tablist">
			<li style="display:none;" class="tab-pager prev-tab-page" role="tab">
				<a href="#" class="tab-item">
					<i class="icon icon-arrow-left"></i>
				</a>
			</li>
			<li style="display:none;" class="tab-pager next-tab-page" role="tab">
				<a href="#" class="tab-item">
					<i class="icon icon-arrow-right"></i>
				</a>
			</li>
			<li>
				<a href="javascript:void(0);" class="tab-item" role="tab" id="en" aria-controls="tab-pane-0" aria-selected="@{{ current_tab_code == 'en' }}" ng-click="getdescription('en')">
					English
				</a>
			</li>
			<li ng-repeat="(code,lang_row) in space.space_descriptions" ng-if="code != 'en'">
				<a href="javascript:void(0);" class="tab-item" role="tab" id="@{{ code }}" aria-controls="tab-pane-0" aria-selected="@{{ current_tab_code == code }}" ng-click="getdescription(code)" ng-cloak>
					@{{ lang_row.language_name }}
				</a>
			</li>
		</ul>
	</div>
	<div class="col-12 col-md-4 add-language mt-3 pl-0 mt-md-0 d-flex flex-wrap justify-content-end align-items-end">
		<a href="javascript:void(0)" class="d-flex align-items-center theme-color" id="add_language" title="{{trans('messages.lys.write_title_and_description')}}" ng-click="addLanguage()">
			<i class="icon icon-add mr-2"></i>
			{{trans('messages.lys.add_language')}}
		</a>
		<a id="delete_language" href="javascript:void(0)" ng-if="current_tab_code != '' && current_tab_code != 'en' " ng-click="deleteLanguage()">
			<i class="icon icon-trash ml-2 green-color mb-0"></i>
		</a>
	</div>
</div>
<div class="description_form mb-4" ng-hide="activeDescTab == 'add_language'">
	<div class="content-heading my-4">
		<h3>
		@lang('messages.lys.amenities_title')
		</h3>
		<p>
			@lang('messages.lys.amenities_desc',['site_name'=>$site_name])
		</p>
	</div>
	<form name="overview">
		<div class="js-section">
			<div class="js-saving-progress saving-progress description1" style="display: none;">
				<h5>
				@lang('messages.lys.saving')...
				</h5>
			</div>
			<div class="mt-2 mb-4" id="help-panel-name">
				<div class="row">
					<div class="col-6">
						<label>
							@lang('messages.lys.listing_name')
						</label>
					</div>
					<div class="col-6">
						<div id="js-name-count" class="text-right">
							<span ng-bind="35 - space.space_descriptions[current_tab_code].name.length">35</span>
							@lang('messages.lys.characters_left')
						</div>
					</div>
				</div>
				<input type="text" name="name" value="{{ @$result->name }}" class="overview-title" placeholder="@lang('messages.lys.name_placeholder')" maxlength="35" ng-model="space.space_descriptions[current_tab_code].name" ng-change="validateStepData('setup','description');">
			</div>
			<div id="help-summary">
				<div class="row">
					<div class="col-6 text_heading">
						<label>
							@lang('messages.lys.summary')
						</label>
					</div>
					<div id="js-summary-count" class="col-6 text_sub_heading text-right">
						<span ng-bind="500 - space.space_descriptions[current_tab_code].summary.length">500</span>
						@lang('messages.lys.characters_left')
					</div>
				</div>
				<textarea class="overview-summary summary_required" name="summary" rows="6" placeholder="@lang('messages.lys.summary_placeholder')" maxlength="500" ng-model="space.space_descriptions[current_tab_code].summary" ng-change="validateStepData('setup','description');">
				{{ @$result->summary }}
				</textarea>
			</div>
		</div>
	</form>
</div>
<div id="add_language_des" class="add_language_info mb-4 text-center" ng-show="activeDescTab == 'add_language'">
	<i class="icon icon-globe green-color mb-2"></i>
	<h3>
	@lang('messages.lys.write_description_other_language')
	</h3>
	<p>
		@lang('messages.lys.site_provide_your_own_version', ['site_name' => $site_name])
	</p>
	<div class="select-language d-flex justify-content-center" ng-init="disableLangAddBtn=true;current_language='';">
		<div class="col-7 p-0 select">
			<select id="language-select" ng-model="current_language" ng-change="disableLangAddBtn=false;">
				<option value="" disabled> @lang('messages.footer.choose_language')... </option>
				<option value="@{{ lang_row.value }}" ng-repeat="lang_row in all_language">
					@{{ lang_row.name }}
				</option>
			</select>
		</div>
		<button class="btn d-flex ml-3 align-items-center" id="write-description-button" ng-click="addlanguageRow()" ng-disabled="disableLangAddBtn">
		<i class="icon icon-add mr-2"></i>
		@lang('messages.lys.add')
		</button>
	</div>
</div>
<p class="my-3 not-post-listed write_more_p" ng-hide="show_more || current_tab_code == ''" ng-click='show_more=true'>
	@lang('messages.lys.you_can_add_more')
	<a href="javascript:void(0);" id="js-write-more" class="theme-color">
		@lang('messages.lys.details')
	</a>
	@lang('messages.lys.tell_travelers_about_your_space')
</p>
<div class="js-section" id="js-section-details" ng-show="show_more">
	<h4>
	@lang('messages.lys.the_trip')
	</h4>
	<div class="mt-2 mb-3" id="help-panel-space">
		<label>
			@lang('messages.lys.the_space')
		</label>
		<textarea name="space" rows="4" ng-model="space.space_descriptions[current_tab_code].space" placeholder="@lang('messages.lys.space_placeholder')" data-saving="description2">
		{{ @$result->rooms_description->space }}
		</textarea>
	</div>
	<div class="my-3" id="help-panel-access">
		<label>
			@lang('messages.lys.guest_access')
		</label>
		<textarea name="access" ng-model="space.space_descriptions[current_tab_code].access" rows="4" placeholder="@lang('messages.lys.guest_access_placeholder')" data-saving="description2">
		{{ @$result->rooms_description->access }}
		</textarea>
	</div>
	<div class="row-space-2" id="help-panel-interaction">
		<label>
			@lang('messages.lys.interaction_with_guests')
		</label>
		<textarea name="interaction" ng-model="space.space_descriptions[current_tab_code].interaction" rows="4" placeholder="@lang('messages.lys.interaction_with_guests_placeholder')" data-saving="description2">
		{{ @$result->rooms_description->interaction }}
		</textarea>
	</div>
	<div class="my-3" id="help-panel-notes">
		<label>
			@lang('messages.lys.other_things_note')
		</label>
		<textarea name="notes" ng-model="space.space_descriptions[current_tab_code].notes" rows="4" placeholder="@lang('messages.lys.other_things_note_placeholder')" data-saving="description2">
		{{ @$result->rooms_description->notes }}
		</textarea>
	</div>
	<div class="my-3" id="help-panel-house-rules">
		<label>
			@lang('messages.setup.space_rules')
		</label>
		<textarea name="house_rules" ng-model="space.space_descriptions[current_tab_code].house_rules" rows="4" placeholder="@lang('messages.lys.house_rules_placeholder')" data-saving="description2">
		{{ @$result->rooms_description->house_rules }}
		</textarea>
	</div>
</div>
<div class="js-section" id="js-section-details_2" style="display:none;">
	<div class="js-saving-progress saving-progress help-panel-neigh-saving description3" style="display: none;">
		<h5>
		@lang('messages.lys.saving') }}...
		</h5>
	</div>
	<h4>
	@lang('messages.lys.the_neighborhood') }}
	</h4>
	<div class="mt-2 mb-3" id="help-panel-neighborhood">
		<label>
			@lang('messages.lys.overview') }}
		</label>
		<textarea name="neighborhood_overview" ng-model="space.space_descriptions[current_tab_code].neighborhood_overview" rows="4" placeholder="@lang('messages.lys.overview_placeholder') }}" data-saving="description3">
		{{ @$result->rooms_description->neighborhood_overview }}
		</textarea>
	</div>
	<div id="help-panel-transit">
		<label>
			@lang('messages.lys.getting_around') }}
		</label>
		<textarea name="transit" ng-model="space.space_descriptions[current_tab_code].transit" rows="4" placeholder="@lang('messages.lys.getting_around_placeholder') }}" data-saving="description3">
		{{ @$result->rooms_description->transit }}
		</textarea>
	</div>
</div>