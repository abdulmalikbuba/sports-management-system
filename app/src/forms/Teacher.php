<?php

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\ConfirmedPasswordField;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FileField;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Member;

class AddTeacherForm extends Form
{
    public function __construct($controller, $name)
    {
        $fields = new FieldList([
            CompositeField::create(
                CompositeField::create(
                    TextField::create(
                        'Surname',
                        'Surname'
                    )->addExtraClass('form-control')
                )->addExtraClass('col-lg-6 col-sm-12'),

                CompositeField::create(
                    TextField::create(
                        'FirstName',
                        'First Name'
                    )->addExtraClass('form-control')
                )->addExtraClass('col-lg-6 col-sm-12'),

                CompositeField::create(
                    EmailField::create(
                        'Email',
                        'Email Address'
                    )->addExtraClass('form-control')
                )->addExtraClass('col-lg-6 col-sm-12'),

                CompositeField::create(
                    DropdownField::create(
                        'Gender',
                        'Gender', ['Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other'])
                    ->addExtraClass('form-control')
                )->addExtraClass('col-lg-6 col-sm-12'),
                
                CompositeField::create(
                    TextField::create(
                        'Telephonenumber',
                        'Phone Number'
                    )->addExtraClass('form-control')
                )->addExtraClass('col-lg-6 col-sm-12'),

                CompositeField::create(
                    TextField::create(
                        'Address',
                        'Address'
                    )->addExtraClass('form-control')
                )->addExtraClass('col-lg-6 col-sm-12'),

                CompositeField::create(
                    DateField::create(
                        'DateOfBirth',
                        'Date Of Birth'
                    )->addExtraClass('form-control')
                )->addExtraClass('col-lg-6 col-sm-12'),

                CompositeField::create(
                    $upload = FileField::create(
                        'ProfilePhoto',
                        'Profile Photo'
                    )->addExtraClass('form-control')
                )->addExtraClass('col-lg-6 col-sm-12'),

                CompositeField::create(
                    DropdownField::create('SportsID', 
                    'Sports',
                    Sports::get()->map('ID', 'Title')) ->setEmptyString('Select Sports')
                    ->addExtraClass('form-control'),
                )->addExtraClass('col-lg-12 col-sm-12'),


                CompositeField::create(
                    ConfirmedPasswordField::create(
                        'Password',
                        'Password'
                    )->addExtraClass('form-control')
                )->addExtraClass('col-lg-12 col-sm-12'),

            )->addExtraClass('row'),

        ]);

        $actions = FieldList::create(
            FormAction::create('save', 'Add Teacher')
            ->setUseButtonTag(true)
            ->addExtraClass('btn btn-success')
        );

        $upload->getValidator()->setAllowedExtensions(['png', 'jpg', 'jpeg', 'gif']);
        $upload->setFolderName('Teacher\'s-Images');

        $validator = new RequiredFields(
            'Surname',
            'FirstName',
            'Email',
            'Gender',
            'Telephonenumber',
            'Password',
        );

        parent::__construct($controller, $name, $fields, $actions, $validator);

    }

    public function save($data, $form,  HTTPRequest $request)
    {
        $session = $request->getSession();

        if (!empty($data['Email']))
        {
            $member = Member::get()->filter("Email", $data['Email'])->first();
            if ($member)
            {
                $form->sessionMessage("Sorry, email is already in use..", 'error');
                return $this->controller->redirect("dashboard/teachers/");
            }
        }

        $member = new Member();

        $form->saveInto($member);

        $member->write();
        
        $member->addToGroupByCode("Users");

        $form->sessionMessage('Teacher has been added successfully!','good');

        return $this->controller->redirect('dashboard/teachers/');
            
    }
}