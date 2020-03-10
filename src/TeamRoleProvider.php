<?php

namespace Tehcodedninja\Teamroles;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class TeamRoleProvider extends ServiceProvider
{

    protected $listen = [
        \Mpociot\Teamwork\Events\UserJoinedTeam::class => [
            Tehcodedninja\Teamroles\Listeners\JoinedTeamListener::class,
        ]
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();
		
		$this->publishMigration();

        $this->registerBladeExtensions();
    }
	
	/**
     * Publish Teamwork configuration
     */
    protected function publishConfig()
    {
        // Publish config files
        $this->publishes([
            __DIR__ . '/Config/teamrole.php' => config_path('teamrole.php'),
        ], 'teamrole-config');
    }
	
	/**
     * Publish TeamRole migration
     */
    protected function publishMigration()
    {
        if (!class_exists('CreateRoleForTeamsTables')) {
            // Publish the migration
            $timestamp = date('Y_m_d_His', time());
            $this->publishes([
                __DIR__ . '/Database/migrations/2016_11_13_134303_create_role_for_teams_tables.php' => database_path('migrations/' . $timestamp . '_create_role_for_teams_tables.php'),
            ], 'teamrole-migrations');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
		$this->mergeConfig();
    }

    public function registerBladeExtensions()
    {
        Blade::directive('teamrole', function ($expression) {
            return "<?php if (Auth::check() && Auth::user()->isTeamRole({$expression})): ?>";
        });

        Blade::directive('endteamrole', function () {
            return "<?php endif; ?>";
        });
    }
	
	/**
     * Merges user's and teamwork's configs.
     *
     * @return void
     */
    protected function mergeConfig()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/teamrole.php', 'teamrole'
        );
    }
}
