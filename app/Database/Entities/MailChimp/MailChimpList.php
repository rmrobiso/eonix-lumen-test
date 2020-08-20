<?php
declare(strict_types=1);

namespace App\Database\Entities\MailChimp;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Utils\Str;

/**
 * @ORM\Entity()
 *
 * non-static properties for entity purpose cannot be private because they are used in parent class
 */
class MailChimpList extends MailChimpEntity
{    
    /**
     * @ORM\Column(name="name", type="string")
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(name="contact", type="array")
     *
     * @var array
     */
    protected $contact;

    /**
     * @ORM\Column(name="permission_reminder", type="string")
     *
     * @var string
     */
    protected $permissionReminder;

    /**
     * @ORM\Column(name="campaign_defaults", type="array")
     *
     * @var array
     */
    protected $campaignDefaults;

    /**
     * @ORM\Column(name="email_type_option", type="boolean")
     *
     * @var bool
     */
    protected $emailTypeOption;

    /**
     * @ORM\Column(name="use_archive_bar", type="boolean", nullable=true)
     *
     * @var bool
     */
    protected $useArchiveBar;

    /**
     * @ORM\Column(name="notify_on_subscribe", type="string", nullable=true)
     *
     * @var string
     */
    protected $notifyOnSubscribe;

    /**
     * @ORM\Column(name="notify_on_unsubscribe", type="string", nullable=true)
     *
     * @var string
     */
    protected $notifyOnUnsubscribe;

    /**
     * @ORM\Column(name="visibility", type="string", nullable=true)
     *
     * @var string
     */
    protected $visibility;

    /**
     * @ORM\Column(name="double_optin", type="boolean", nullable=true)
     *
     * @var bool
     */
    protected $double_optin;

    /**
     * @ORM\Column(name="marketing_permissions", type="boolean", nullable=true)
     *
     * @var bool
     */
    protected $marketing_permissions;

    /**
     * The unique id for the list in own DB
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    protected $listId;

    /**
     * The unique id for the list on remote Mailchimp server
     * @ORM\Column(name="mail_chimp_id", type="string", nullable=true)
     *
     * @var string
     */
    protected $mailChimpId; 


    /**
     * Set campaign defaults.
     *
     * @param array $campaignDefaults
     *
     * @return MailChimpList
     */
    public function setCampaignDefaults(array $campaignDefaults): MailChimpList
    {
        $this->campaignDefaults = $campaignDefaults;

        return $this;
    }

    /**
     * Set contact.
     *
     * @param array $contact
     *
     * @return MailChimpList
     */
    public function setContact(array $contact): MailChimpList
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Set email type option.
     *
     * @param bool $emailTypeOption
     *
     * @return MailChimpList
     */
    public function setEmailTypeOption(bool $emailTypeOption): MailChimpList
    {
        $this->emailTypeOption = $emailTypeOption;

        return $this;
    }

    /**
     * Set mailchimp id of the list.
     *
     * @param string $mailChimpId
     *
     * @return MailChimpList
     */
    public function setMailChimpId(string $mailChimpId): MailChimpList
    {
        $this->mailChimpId = $mailChimpId;

        return $this;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return MailChimpList
     */
    public function setName(string $name): MailChimpList
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set notify on subscribe.
     *
     * @param string $notifyOnSubscribe
     *
     * @return MailChimpList
     */
    public function setNotifyOnSubscribe(string $notifyOnSubscribe): MailChimpList
    {
        $this->notifyOnSubscribe = $notifyOnSubscribe;

        return $this;
    }

    /**
     * Set notify on unsubscribe.
     *
     * @param string $notifyOnUnsubscribe
     *
     * @return MailChimpList
     */
    public function setNotifyOnUnsubscribe(string $notifyOnUnsubscribe): MailChimpList
    {
        $this->notifyOnUnsubscribe = $notifyOnUnsubscribe;

        return $this;
    }

    /**
     * Set permission reminder.
     *
     * @param string $permissionReminder
     *
     * @return MailChimpList
     */
    public function setPermissionReminder(string $permissionReminder): MailChimpList
    {
        $this->permissionReminder = $permissionReminder;

        return $this;
    }

    /**
     * Set use archive bar.
     *
     * @param bool $useArchiveBar
     *
     * @return MailChimpList
     */
    public function setUseArchiveBar(bool $useArchiveBar): MailChimpList
    {
        $this->useArchiveBar = $useArchiveBar;

        return $this;
    }

    /**
     * Set visibility.
     *
     * @param string $visibility
     *
     * @return MailChimpList
     */
    public function setVisibility(string $visibility): MailChimpList
    {
        $this->visibility = $visibility;

        return $this;
    }

    
    /**
     * Get id.
     *
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->listId;
    }

    /**
     * Get mailchimp id of the list.
     *
     * @return null|string
     */
    public function getMailChimpId(): ?string
    {
        return $this->mailChimpId;
    }

    /**
     * Get validation rules for mailchimp entity.
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        return [
            'campaign_defaults' => 'required|array',
            'campaign_defaults.from_name' => 'required|string',
            'campaign_defaults.from_email' => 'required|string',
            'campaign_defaults.subject' => 'required|string',
            'campaign_defaults.language' => 'required|string',
            'contact' => 'required|array',
            'contact.company' => 'required|string',
            'contact.address1' => 'required|string',
            'contact.address2' => 'nullable|string',
            'contact.city' => 'required|string',
            'contact.state' => 'required|string',
            'contact.zip' => 'required|string',
            'contact.country' => 'required|string|size:2',
            'contact.phone' => 'nullable|string',
            'email_type_option' => 'required|boolean',
            'name' => 'required|string',
            'notify_on_subscribe' => 'nullable|email',
            'notify_on_unsubscribe' => 'nullable|email',
            'mailchimp_id' => 'nullable|string',
            'permission_reminder' => 'required|string',
            'use_archive_bar' => 'nullable|boolean',
            'visibility' => 'nullable|string|in:pub,prv'
        ];
    }

}
