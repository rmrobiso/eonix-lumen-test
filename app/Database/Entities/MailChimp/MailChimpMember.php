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
class MailChimpMember extends MailChimpEntity
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    protected $memberId;

    /**
     * @ORM\Column(name="list_id", type="string", nullable=false)
     *
     * @var string
     */
    protected $listId;

    /**
     * @ORM\Column(name="mail_chimp_id", type="string", nullable=true)
     *
     * @var string
     */
    protected $mailChimpId;

    /**
     * @ORM\Column(name="email_address", type="string", nullable=false)
     *
     * @var string
     */
    protected $emailAddress;

    /**
     * @ORM\Column(name="status", type="string", nullable=false)
     *
     * @var string
     */
    protected $status;

    /**
     * @ORM\Column(name="email_type", type="string", nullable=true)
     *
     * @var string
     */
    protected $email_type;
    
    /**
     * @ORM\Column(name="language", type="string", nullable=true)
     *
     * @var string
     */
    protected $language;

    /**
     * @ORM\Column(name="vip", type="boolean", nullable=true)
     *
     * @var boolean
     */
    protected $vip;

    /**
     * @ORM\Column(name="location", type="array", nullable=true)
     *
     * @var array
     * items: latitude, longitude
     */
    protected $location;

    /**
     * @ORM\Column(name="marketing_permissions", type="array", nullable=true)
     *
     * @var array
     * items: marketing_permission_id, enabled
     */
    protected $marketing_permissions;

    /**
     * @ORM\Column(name="ip_signup", type="string", nullable=true)
     *
     * @var string
     */
    protected $ipSignup;

    /**
     * @ORM\Column(name="timestamp_signup", type="string", nullable=true)
     *
     * @var string
     */
    protected $timestamp_signup;

    /**
     * @ORM\Column(name="ip_opt", type="string", nullable=true)
     *
     * @var string
     */
    protected $ip_opt;

    /**
     * @ORM\Column(name="timestamp_opt", type="string", nullable=true)
     *
     * @var string
     */
    protected $timestamp_opt;

    /**
     * @ORM\Column(name="tags", type="array", nullable=true)
     *
     * @var array
     */
    protected $tags;

    /**
     * @ORM\Column(name="email_id", type="string", nullable=true)
     *
     * @var string
     */
    protected $emailId;

    /**
     * @ORM\Column(name="unique_email_id", type="string", nullable=true)
     *
     * @var string
     */
    protected $uniqueEmailId;

    /**
     * @ORM\Column(name="member_rating", type="integer", nullable=true)
     *
     * @var int
     */
    protected $memberRating;

        
    /**
     * Set mailchimp id of the member.
     *
     * @param string $mailChimpId
     *
     * @return MailChimpMember
     */
    public function setMailChimpId(string $mailChimpId): MailChimpMember
    {
        $this->mailChimpId = $mailChimpId;

        return $this;
    }

    /**
     * Set list id of the member.
     *
     * @param string $listId
     *
     * @return MailChimpMember
     */
    public function setListId(string $listId): MailChimpMember
    {
        $this->listId = $listId;

        return $this;
    }

    /**
     * Set email address.
     *
     * @param string $emailAddress
     *
     * @return MailChimpMember
     */
    public function setEmailAddress(string $emailAddress): MailChimpMember
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return MailChimpMember
     */
    public function setStatus(string $status): MailChimpMember
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Set language.
     *
     * @param string $language
     *
     * @return MailChimpMember
     */
    public function setLanguage(string $language): MailChimpMember
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Set vip.
     *
     * @param bool $vip
     *
     * @return MailChimpMember
     */
    public function setVip(bool $vip): MailChimpMember
    {
        $this->vip = $vip;

        return $this;
    }

    /**
     * Set location.
     *
     * @param array $location
     *
     * @return MailChimpMember
     */
    public function setLocation(array $location): MailChimpMember
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Set ip sign up.
     *
     * @param string $ipSignup
     *
     * @return MailChimpMember
     */
    public function setIpSignup(string $ipSignup): MailChimpMember
    {
        $this->ipSignup = $ipSignup;

        return $this;
    }

    /**
     * Set tags.
     *
     * @param array $tags
     *
     * @return MailChimpMember
     */
    public function setTags(array $tags): MailChimpMember
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Set email ID.
     *
     * @param string $emailId
     *
     * @return MailChimpMember
     */
    public function setEmailId(string $emailId): MailChimpMember
    {
        $this->emailId = $emailId;

        return $this;
    }

    /**
     * Set unique email ID.
     *
     * @param string $uniqueEmailId
     *
     * @return MailChimpMember
     */
    public function setUniqueEmailId(string $uniqueEmailId): MailChimpMember
    {
        $this->uniqueEmailId = $uniqueEmailId;

        return $this;
    }

    /**
     * Set member rating.
     *
     * @param int $memberRating
     *
     * @return MailChimpMember
     */
    public function setMemberRating(string $memberRating): MailChimpMember
    {
        $this->memberRating = $memberRating;

        return $this;
    }


    /**
     * Get id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->memberId;
    }

    /**
     * Get mailchimp id of the member.
     *
     * @return null|string
     */
    public function getMailChimpId(): ?string
    {
        return $this->mailChimpId;
    }

    /**
     * Get email address
     * @return string
     */
    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    /**
     * Get validation rules for mailchimp entity.
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        return [
            'email_address' => 'required|email|string',
            'status' => 'required|string',
            'language' => 'nullable|string',
            'vip' => 'nullable|boolean',
            'location.latitude' => 'nullable|string',
            'location.longitude' => 'nullable|string',
            'ip_signup' => 'nullable|ip|string',
            'tags' => 'nullable|array',
        ];
    }

}
