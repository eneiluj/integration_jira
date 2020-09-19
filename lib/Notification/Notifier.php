<?php
/**
 * Nextcloud - jira
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julien Veyssier <eneiluj@posteo.net>
 * @copyright Julien Veyssier 2019
 */

namespace OCA\Jira\Notification;


use OCP\IURLGenerator;
use OCP\IUserManager;
use OCP\L10N\IFactory;
use OCP\Notification\IManager as INotificationManager;
use OCP\Notification\INotification;
use OCP\Notification\INotifier;
use OCA\Jira\AppInfo\Application;

class Notifier implements INotifier {

	/** @var IFactory */
	protected $factory;

	/** @var IUserManager */
	protected $userManager;

	/** @var INotificationManager */
	protected $notificationManager;

	/** @var IURLGenerator */
	protected $url;

	/**
	 * @param IFactory $factory
	 * @param IUserManager $userManager
	 * @param INotificationManager $notificationManager
	 * @param IURLGenerator $urlGenerator
	 */
	public function __construct(IFactory $factory,
								IUserManager $userManager,
								INotificationManager $notificationManager,
								IURLGenerator $urlGenerator) {
		$this->factory = $factory;
		$this->userManager = $userManager;
		$this->notificationManager = $notificationManager;
		$this->url = $urlGenerator;
	}

	/**
	 * Identifier of the notifier, only use [a-z0-9_]
	 *
	 * @return string
	 * @since 17.0.0
	 */
	public function getID(): string {
		return 'integration_jira';
	}
	/**
	 * Human readable name describing the notifier
	 *
	 * @return string
	 * @since 17.0.0
	 */
	public function getName(): string {
		return $this->lFactory->get('integration_jira')->t('Jira');
	}

	/**
	 * @param INotification $notification
	 * @param string $languageCode The code of the language that should be used to prepare the notification
	 * @return INotification
	 * @throws \InvalidArgumentException When the notification was not prepared by a notifier
	 * @since 9.0.0
	 */
	public function prepare(INotification $notification, string $languageCode): INotification {
		if ($notification->getApp() !== 'integration_jira') {
			// Not my app => throw
			throw new \InvalidArgumentException();
		}

		$l = $this->factory->get('integration_jira', $languageCode);

		switch ($notification->getSubject()) {
		case 'new_open_tickets':
			$p = $notification->getSubjectParameters();
			$content = $l->t('You have %s open issues with recent activity in Jira.', [$p['nbOpen'] ?? 0]);

			//$theme = $this->config->getUserValue($userId, 'accessibility', 'theme', '');
			//$iconUrl = ($theme === 'dark')
			//	? $this->url->imagePath(Application::APP_ID, 'app.svg')
			//	: $this->url->imagePath(Application::APP_ID, 'app-dark.svg');

			$notification->setParsedSubject($content)
				->setLink($p['link'] ?? '')
				->setIcon($this->url->getAbsoluteURL($this->url->imagePath(Application::APP_ID, 'app-dark.svg')));
				//->setIcon($this->url->getAbsoluteURL($iconUrl));
			return $notification;

		default:
			// Unknown subject => Unknown notification => throw
			throw new \InvalidArgumentException();
		}
	}
}