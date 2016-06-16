<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\DataFixtures\Tests;

use AppBundle\DataFixtures\AltrEgoTrait;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eTraxis\Dictionary\EventType;
use eTraxis\Entity\Event;
use eTraxis\Entity\FieldValue;
use eTraxis\Entity\LastRead;
use eTraxis\Entity\Record;
use eTraxis\Entity\StringValue;
use eTraxis\Entity\TextValue;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadPhpPsrRecordsData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    use AltrEgoTrait;

    /** @var ContainerInterface */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 9;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $records = [
            0 => [
                'subject'     => 'Autoloading Standard',
                'author'      => 'user:mwop',
                'draft'       => '2010-11-05 17:58 GMT+13',
                'accepted'    => '2012-10-12 08:54 GMT+13',
                'deprecated'  => '2014-10-08 13:04 GMT+13',
                'postponed'   => null,
                'description' => 'The following describes the mandatory requirements that must be adhered to for autoloader interoperability.',
            ],
            1 => [
                'subject'     => 'Basic Coding Standard',
                'author'      => 'user:pmjones',
                'draft'       => '2012-03-12 13:39 GMT+13',
                'accepted'    => '2012-06-05 05:02 GMT+13',
                'deprecated'  => null,
                'postponed'   => null,
                'description' => 'This section of the standard comprises what should be considered the standard coding elements that are required to ensure a high level of technical interoperability between shared PHP code.',
            ],
            2 => [
                'subject'     => 'Coding Style Guide',
                'author'      => 'user:pmjones',
                'draft'       => '2012-05-05 03:04 GMT+13',
                'accepted'    => '2012-06-05 05:02 GMT+13',
                'deprecated'  => null,
                'postponed'   => null,
                'description' => 'The intent of this guide is to reduce cognitive friction when scanning code from different authors. It does so by enumerating a shared set of rules and expectations about how to format PHP code.' . "\n" .
                                 'The style rules herein are derived from commonalities among the various member projects. When various authors collaborate across multiple projects, it helps to have one set of guidelines to be used among all those projects. Thus, the benefit of this guide is not in the rules themselves, but in the sharing of those rules.',
            ],
            3 => [
                'subject'     => 'Logger Interface',
                'author'      => 'user:seldaek',
                'draft'       => '2012-12-14 09:41 GMT+13',
                'accepted'    => '2013-01-06 17:50 GMT+13',
                'deprecated'  => null,
                'postponed'   => null,
                'description' => 'This document describes a common interface for logging libraries.' . "\n" .
                                 'The main goal is to allow libraries to receive a Psr\Log\LoggerInterface object and write logs to it in a simple and universal way. Frameworks and CMSs that have custom needs MAY extend the interface for their own purpose, but SHOULD remain compatible with this document. This ensures that the third-party libraries an application uses can write to the centralized application logs.',
            ],
            4 => [
                'subject'     => 'Autoloading Standard',
                'author'      => 'user:pmjones',
                'draft'       => '2013-08-08 02:41 GMT+13',
                'accepted'    => '2013-12-04 05:49 GMT+13',
                'deprecated'  => null,
                'postponed'   => null,
                'description' => 'This PSR describes a specification for [url=http://php.net/autoload]autoloading[/url] classes from file paths. It is fully interoperable, and can be used in addition to any other autoloading specification, including PSR-0. This PSR also describes where to place files that will be autoloaded according to the specification.',
            ],
            5 => [
                'subject'     => 'PHPDoc Standard',
                'author'      => 'user:mvriel',
                'draft'       => '2013-08-06 06:35 GMT+13',
                'accepted'    => null,
                'deprecated'  => null,
                'postponed'   => '2015-12-10 18:20 GMT+13',
                'description' => 'The main purpose of this PSR is to provide a complete and formal definition of the PHPDoc standard. This PSR deviates from its predecessor, the de-facto PHPDoc Standard associated with [url=http://www.phpdoc.org/]phpDocumentor 1.x[/url], to provide support for newer features in the PHP language and to address some of the shortcomings of its predecessor.',
            ],
            6 => [
                'subject'     => 'Caching Interface',
                'author'      => 'user:Crell',
                'draft'       => '2013-08-14 03:50 GMT+13',
                'accepted'    => '2015-12-11 15:22 GMT+13',
                'deprecated'  => null,
                'postponed'   => null,
                'description' => 'Caching is a common way to improve the performance of any project, making caching libraries one of the most common features of many frameworks and libraries. This has lead to a situation where many libraries roll their own caching libraries, with various levels of functionality. These differences are causing developers to have to learn multiple systems which may or may not provide the functionality they need. In addition, the developers of caching libraries themselves face a choice between only supporting a limited number of frameworks or creating a large number of adapter classes.' . "\n" .
                                 'A common interface for caching systems will solve these problems. Library and framework developers can count on the caching systems working the way they\'re expecting, while the developers of caching systems will only have to implement a single set of interfaces rather than a whole assortment of adapters.',
            ],
            7 => [
                'subject'     => 'HTTP Message Interface',
                'author'      => 'user:mwop',
                'draft'       => '2014-01-14 18:23 GMT+13',
                'accepted'    => '2015-05-19 13:31 GMT+13',
                'deprecated'  => null,
                'postponed'   => null,
                'description' => 'This document describes common interfaces for representing HTTP messages as described in [url=http://tools.ietf.org/html/rfc7230]RFC 7230[/url] and [url=http://tools.ietf.org/html/rfc7231]RFC 7231[/url], and URIs for use with HTTP messages as described in [url=http://tools.ietf.org/html/rfc3986]RFC 3986[/url].' . "\n" .
                                 'HTTP messages are the foundation of web development. Web browsers and HTTP clients such as cURL create HTTP request messages that are sent to a web server, which provides an HTTP response message. Server-side code receives an HTTP request message, and returns an HTTP response message.' . "\n" .
                                 'HTTP messages are typically abstracted from the end-user consumer, but as developers, we typically need to know how they are structured and how to access or manipulate them in order to perform our tasks, whether that might be making a request to an HTTP API, or handling an incoming request.',
            ],
            8 => [
                'subject'     => 'Huggable Interface',
                'author'      => 'user:Crell',
                'draft'       => '2014-02-23 11:17 GMT+13',
                'accepted'    => null,
                'deprecated'  => null,
                'postponed'   => null,
                'description' => 'This standard establishes a common way for objects to express mutual appreciation and support by hugging. This allows objects to support each other in a constructive fashion, furthering cooperation between different PHP projects.',
            ],
            9 => [
                'subject'     => 'Security Advisories',
                'author'      => 'user:lsmith',
                'draft'       => '2014-11-05 22:57 GMT+13',
                'accepted'    => null,
                'deprecated'  => null,
                'postponed'   => null,
                'description' => 'There are two aspects with dealing with security issues: One is the process by which security issues are reported and fixed in projects, the other is how the general public is informed about the issues and any remedies available. While PSR-9 addresses the former, this PSR, ie. PSR-10, deals with the later. So the goal of PSR-10 is to define how security issues are disclosed to the public and what format such disclosures should follow. Especially today where PHP developers are sharing code across projects more than ever, this PSR aims to ease the challenges in keeping an overview of security issues in all dependencies and the steps required to address them.' . "\n" .
                                 'The goal of this PSR is to give project leads a clearly defined approach to enabling end users to discover security disclosures using a clearly defined structured format for these disclosures.',
            ],
            10 => [
                'subject'     => 'Security Reporting Process',
                'author'      => 'user:lsmith',
                'draft'       => '2015-02-09 05:08 GMT+13',
                'accepted'    => null,
                'deprecated'  => null,
                'postponed'   => null,
                'description' => 'There are two aspects with dealing with security issues: One is the process by which security issues are reported and fixed in projects, the other is how the general public is informed about the issues and any remedies available. While PSR-10 addresses the later, this PSR, ie. PSR-9, deals with the former. So the goal of PSR-9 is to define the process by which security researchers and report security vulnerabilities to projects. It is important that when security vulnerabilities are found that researchers have an easy channel to the projects in question allowing them to disclose the issue to a controlled group of people.' . "\n" .
                                 'The goal of this PSR is to give researchers, project leads, upstream project leads and end users a defined and structured process for disclosing security vulnerabilities.',
            ],
            11 => [
                'subject'     => 'Container Interface',
                'author'      => 'user:moufmouf',
                'draft'       => '2015-04-02 02:18 GMT+13',
                'accepted'    => null,
                'deprecated'  => null,
                'postponed'   => '2015-10-20 01:08 GMT+13',
                'description' => 'This document describes a common interface for dependency injection containers.' . "\n" .
                                 'The goal set by [code]ContainerInterface[/code] is to standardize how frameworks and libraries make use of a container to obtain objects and parameters (called [i]entries[/i] in the rest of this document).',
            ],
            12 => [
                'subject'     => 'Extended Coding Style Guide',
                'author'      => 'user:korvinszanto',
                'draft'       => '2015-08-24 11:56 GMT+13',
                'accepted'    => null,
                'deprecated'  => null,
                'postponed'   => null,
                'description' => 'This specification extends, expands and replaces PSR-2, the coding style guide and requires adherance to PSR-1, the basic coding standard.' . "\n" .
                                 'Like PSR-2, the intent of this specification is to reduce cognitive friction when scanning code from different authors. It does so by enumerating a shared set of rules and expectations about how to format PHP code. This PSR seeks to provide a set way that coding style tools can implement, projects can declare adherence to and developers can easily relate to between different projects. When various authors collaborate across multiple projects, it helps to have one set of guidelines to be used among all those projects. Thus, the benefit of this guide is not in the rules themselves but the sharing of those rules.' . "\n" .
                                 'PSR-2 was accepted in 2012 and since then a number of changes have been made to PHP which have implications for coding style guidelines. Whilst PSR-2 is very comprehensive of PHP functionality that existed at the time of writing, new functionality is very open to interpretation. This PSR therefore seeks to clarify the content of PSR-2 in a more modern context with new functionality available, and make the errata to PSR-2 binding.',
            ],
            13 => [
                'subject'     => 'Hypermedia Links',
                'author'      => 'user:Crell',
                'draft'       => '2015-10-28 11:54 GMT+13',
                'accepted'    => null,
                'deprecated'  => null,
                'postponed'   => null,
                'description' => 'Hypermedia links are becoming an increasingly important part of the web, in both HTML contexts and various API format contexts. However, there is no single common hypermedia format, nor is there a common way to represent Links between formats.' . "\n" .
                                 'This specification aims to provide PHP developers with a simple, common way of representing a hypermedia link independently of the serialization format that is used. That in turn allows a system to serialize a response with hypermedia links into one or more wire formats independently of the process of deciding what those links should be.',
            ],
            14 => [
                'subject'     => 'Event Manager',
                'author'      => 'user:manchuck',
                'draft'       => '2016-03-18 13:50 GMT+13',
                'accepted'    => null,
                'deprecated'  => null,
                'postponed'   => null,
                'description' => 'Event Dispatching allows developer to inject logic into an application easily. Many frameworks implement some form of a event dispatching that allows users to inject functionality with the need to extend classes.',
            ],
        ];

        $state_draft      = $this->getReference('state:psr:draft');
        $state_accepted   = $this->getReference('state:psr:accepted');
        $state_deprecated = $this->getReference('state:psr:deprecated');

        foreach ($records as $id => $info) {

            $class = new \ReflectionClass(Record::class);

            /** @var Record $record */
            $record = $class->newInstanceWithoutConstructor();

            $record->setSubject($info['subject']);

            $altr_record = $this->ego($record);

            $altr_record->state     = $state_draft;
            $altr_record->author    = $this->getReference($info['author']);
            $altr_record->createdAt = strtotime($info['draft']);
            $altr_record->changedAt = strtotime($info['draft']);

            $event = new Event(
                $record,
                $record->getAuthor(),
                EventType::RECORD_CREATED,
                $state_draft->getId()
            );

            $this->ego($event)->createdAt = $record->getCreatedAt();

            $psrId = new StringValue($id);

            $manager->persist($record);
            $manager->persist($event);
            $manager->persist($psrId);

            $manager->flush();

            $field1 = new FieldValue();

            $this->ego($field1)->event     = $event;
            $this->ego($field1)->field     = $this->getReference('state:psr:draft:1');
            $this->ego($field1)->isCurrent = true;
            $this->ego($field1)->value     = $psrId->getId();

            $field2 = new FieldValue();

            $this->ego($field2)->event     = $event;
            $this->ego($field2)->field     = $this->getReference('state:psr:draft:2');
            $this->ego($field2)->isCurrent = true;

            if ($info['description']) {

                $description = new TextValue($info['description']);

                $manager->persist($description);
                $manager->flush();

                $this->ego($field2)->value = $description->getId();
            }

            $manager->persist($field2);

            if ($info['accepted'] !== null) {

                $event = new Event(
                    $record,
                    $record->getAuthor(),
                    EventType::STATE_CHANGED,
                    $state_accepted->getId()
                );

                $this->ego($event)->createdAt = strtotime($info['accepted']);

                $manager->persist($event);

                $altr_record->changedAt = $this->ego($event)->createdAt;
                $altr_record->state     = $state_accepted;

                $manager->persist($record);
            }

            if ($info['deprecated'] !== null) {

                $event = new Event(
                    $record,
                    $record->getAuthor(),
                    EventType::STATE_CHANGED,
                    $state_deprecated->getId()
                );

                $this->ego($event)->createdAt = strtotime($info['deprecated']);

                $manager->persist($event);

                $altr_record->changedAt = $this->ego($event)->createdAt;
                $altr_record->closedAt  = $this->ego($event)->createdAt;
                $altr_record->state     = $state_deprecated;

                $manager->persist($record);
            }

            if ($info['postponed'] !== null) {

                $event = new Event(
                    $record,
                    $record->getAuthor(),
                    EventType::RECORD_POSTPONED,
                    time() + 86400
                );

                $this->ego($event)->createdAt = strtotime($info['postponed']);

                $manager->persist($event);

                $altr_record->changedAt = $this->ego($event)->createdAt;
                $altr_record->resumedAt = $this->ego($event)->parameter;

                $manager->persist($record);
            }

            $read = new LastRead($record, $record->getAuthor());

            $this->ego($read)->readAt = $record->getChangedAt();

            $manager->persist($read);
        }

        $manager->flush();
    }
}
