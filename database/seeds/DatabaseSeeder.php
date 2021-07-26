<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(SiteSettingsTableSeeder::class);
        $this->call(CurrencyTableSeeder::class);
        $this->call(LanguageTableSeeder::class);
        $this->call(CountryTableSeeder::class);
        $this->call(MessageTypeTableSeeder::class);
        $this->call(TimezoneTableSeeder::class);
        $this->call(DateformatsTableSeeder::class);

        $this->call(KindOfSpaceTableSeeder::class);
        $this->call(GuestAccessTableSeeder::class);
        $this->call(SpaceRulesTableSeeder::class);
        $this->call(ServicesTableSeeder::class);
        $this->call(StylesTableSeeder::class);
        $this->call(SpecialFeaturesTableSeeder::class);
        $this->call(AmenitiesTableSeeder::class);
        $this->call(MetasTableSeeder::class);

        $this->call(ActivitiesTypeTableSeeder::class);
        $this->call(ActivitiesTableSeeder::class);
        $this->call(SubActivitiesTableSeeder::class);

        $this->call(ApiCredentialsTableSeeder::class);
        $this->call(PaymentGatewayTableSeeder::class);
        $this->call(EmailSettingsTableSeeder::class);
        $this->call(FeesTableSeeder::class);
        $this->call(ReferralSettingsTableSeeder::class);

        $this->call(RolesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(AdminTableSeeder::class);

        $this->call(PagesTableSeeder::class);
        $this->call(JoinUsTableSeeder::class);
        $this->call(SliderTableSeeder::class);
        $this->call(HomePageSlidersTableSeeder::class);
        $this->call(OurCommunityBannersTableSeeder::class);

        $this->call(HelpCategoryTableSeeder::class);
        $this->call(HelpSubCategoryTableSeeder::class);
        $this->call(HelpTableSeeder::class);

        // $this->call(UsersTableSeeder::class);
        // $this->call(PayoutPreferencesTableSeeder::class);
        // $this->call(SpaceTableSeeder::class);

        // $this->call(ReservationTableSeeder::class);
        // $this->call(MessagesTableSeeder::class);
        // $this->call(PayoutsTableSeeder::class);
        // $this->call(DisputeTableSeeder::class);
        // $this->call(ReviewsTableSeeder::class);

        // $this->call(WishlistsTableSeeder::class);
        // $this->call(ReferralsTableSeeder::class);

        Model::reguard();
    }
}