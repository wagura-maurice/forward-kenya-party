a need to create new political part onboaring page in larvel using inartia js an vuse js for the js that will eventurse via the web.php route work wtih laravel to enuare there ia wa wizard crete in vue that can onboard user to a politiacl pary website based on the scheba logi of larvel

roles
abilites
ability_roles
users
role_users

region
county
sub-county
constituency
ward
location
village
polling-staion

i.e.b.c interface table - the way our party member mapped all the way to the ppolling station mergeis with thr iebc logic in ensure adherance to the data we can publish in ter of oue offiver vereifie memebres.

-   the location scope is about keeping the users in athe store to a way to can map them up to the polling staion level

profiles - just store the kyc of the user i.e is tied to the user table fia user_id and thus get ti currey extar detail abut the human face behide the account i.e telephon, adreee, etc on ahuma level

create a wizard page that will send all the data to the default register route in larvel but naow the rout is poated will all this data being crolled from a uniform larvel schema that is the use in tailwinf ro come iup with a well step by step onboareing mechanism

username
email
password
profile_photo_path_to_storage - https secure path using cloudflare r2 foe the storage

telephone
salutation
first_name
middle_name
last_name
gender
address_line_1
address_line_2
city
state
country
date_of_birth
occupation
education_level
employer_details
proof_of_address
proof_of_identity
security_question
security_answer

<!-- dynamic drop down fro loacation details foe administrivaive popeorse -->

country_id - can be any.
region_id - we aonly store logations feom kenya so enaure the are asked about the person registering beu the andser shoual accoun that the person colusn be liveing in kenya so ensure to show onaly the slect drop dowoen dynamicay in the flow of top to bottom kenya admin logic of county, region (province), county (distict), sub_county, constinuency, ward, loaction, village, can be use to mape people in kenya pahysicaly addess wise.
county_id - we aonly store logations feom kenya so enaure the are asked about the person registering beu the andser shoual accoun that the person colusn be liveing in kenya so ensure to show onaly the slect drop dowoen dynamicay in the flow of top to bottom kenya admin logic of county, region (province), county (distict), sub_county, constinuency, ward, loaction, village, can be use to mape people in kenya pahysicaly addess wise.
sub_county_id - we aonly store logations feom kenya so enaure the are asked about the person registering beu the andser shoual accoun that the person colusn be liveing in kenya so ensure to show onaly the slect drop dowoen dynamicay in the flow of top to bottom kenya admin logic of county, region (province), county (distict), sub_county, constinuency, ward, loaction, village, can be use to mape people in kenya pahysicaly addess wise.
constituency_id - we aonly store logations feom kenya so enaure the are asked about the person registering beu the andser shoual accoun that the person colusn be liveing in kenya so ensure to show onaly the slect drop dowoen dynamicay in the flow of top to bottom kenya admin logic of county, region (province), county (distict), sub_county, constinuency, ward, loaction, village, can be use to mape people in kenya pahysicaly addess wise.
ward_id - we aonly store logations feom kenya so enaure the are asked about the person registering beu the andser shoual accoun that the person colusn be liveing in kenya so ensure to show onaly the slect drop dowoen dynamicay in the flow of top to bottom kenya admin logic of county, region (province), county (distict), sub_county, constinuency, ward, loaction, village, can be use to mape people in kenya pahysicaly addess wise.
location_id - we aonly store logations feom kenya so enaure the are asked about the person registering beu the andser shoual accoun that the person colusn be liveing in kenya so ensure to show onaly the slect drop dowoen dynamicay in the flow of top to bottom kenya admin logic of county, region (province), county (distict), sub_county, constinuency, ward, loaction, village, can be use to mape people in kenya pahysicaly addess wise.
village_id - we aonly store logations feom kenya so enaure the are asked about the person registering beu the andser shoual accoun that the person colusn be liveing in kenya so ensure to show onaly the slect drop dowoen dynamicay in the flow of top to bottom kenya admin logic of county, region (province), county (distict), sub_county, constinuency, ward, loaction, village, can be use to mape people in kenya pahysicaly addess wise.

polling_station_id only if they are citizens? but is not a must to be ansewerd. can be sent null

consulate -only whey they are non citizens? but is not a must to be ansewerd. can be sent null

refugee- only whey they refugees an is nullable.


php artisan make:model BankType -m
php artisan make:model BankCategory -m
php artisan make:model Bank -m

php artisan make:controller API/BankTypeController --api
php artisan make:controller API/BankCategoryController --api
php artisan make:controller API/BankController --api

php artisan make:resource API/BankTypeResource
php artisan make:resource API/BankCategoryResource
php artisan make:resource API/BankResource

php artisan make:request API/BankTypeRequest
php artisan make:request API/BankCategoryRequest
php artisan make:request API/BankRequest


php artisan make:policy BankTypePolicy --model=BankType
php artisan make:policy BankCategoryPolicy --model=BankCategory
php artisan make:policy BankPolicy --model=Bank

php artisan make:seeder BankTypeSeeder
php artisan make:seeder BankCategorySeeder
php artisan make:seeder BankSeeder
