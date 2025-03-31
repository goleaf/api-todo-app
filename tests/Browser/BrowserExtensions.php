<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;

/**
 * This trait provides extensions to the Laravel Dusk Browser class.
 */
trait BrowserExtensions
{
    /**
     * Wait for a page reload to complete.
     *
     * @param  int  $milliseconds
     * @return \Laravel\Dusk\Browser
     */
    public function waitForReload($milliseconds = 5000)
    {
        $this->pause($milliseconds);
        
        return $this;
    }
    
    /**
     * Wait for an AJAX request to complete.
     *
     * @param  int  $milliseconds
     * @return \Laravel\Dusk\Browser
     */
    public function waitForAjax($milliseconds = 2000)
    {
        $this->pause($milliseconds);
        
        return $this;
    }
    
    /**
     * Search using a form.
     *
     * @param  string  $searchText
     * @param  string  $inputSelector
     * @param  string  $buttonSelector
     * @return \Laravel\Dusk\Browser
     */
    public function search($searchText, $inputSelector = 'input[name="search"]', $buttonSelector = 'button[type="submit"]')
    {
        return $this->type($inputSelector, $searchText)
                   ->click($buttonSelector)
                   ->waitForReload();
    }
    
    /**
     * Navigate to an admin section.
     *
     * @param  string  $section
     * @return \Laravel\Dusk\Browser
     */
    public function navigateToAdminSection($section)
    {
        return $this->clickLink(ucfirst($section))
                   ->waitForLocation('/admin/' . strtolower($section));
    }
    
    /**
     * Login as an admin user.
     *
     * @param  string  $email
     * @param  string  $password
     * @return \Laravel\Dusk\Browser
     */
    public function loginAsAdmin($email, $password)
    {
        return $this->visit('/admin/login')
                   ->type('email', $email)
                   ->type('password', $password)
                   ->press('Login')
                   ->waitForLocation('/admin/dashboard');
    }
} 