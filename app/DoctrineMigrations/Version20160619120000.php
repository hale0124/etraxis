<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use eTraxis\Dictionary\AuthenticationProvider;
use eTraxis\Dictionary\FieldPermission;
use eTraxis\Dictionary\Legacy;
use eTraxis\Dictionary\SystemRole;
use eTraxis\Migrations\BaseMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * 3.9.x => 4.0.0
 */
class Version20160619120000 extends BaseMigration implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    protected $container;

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
    public function getVersion()
    {
        return '4.0.0';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema)
    {
        // Check current version.
        $version = $this->connection->fetchColumn('SELECT var_value FROM tbl_sys_vars WHERE var_name = :variable', [
            'variable' => 'FEATURE_LEVEL',
        ]);

        $this->abortIf($version !== '3.9', sprintf('eTraxis 3.9 is expected, %s was found.', $version));

        // Import existing data from legacy tables.
        $this->migrateAccounts();
        $this->migrateProjects();
        $this->migrateGroups();
        $this->migrateMembership();
        $this->migrateTemplates();
        $this->migrateGroupPerms();
        $this->migrateStates();
        $this->migrateStateAssignees();
        $this->migrateGroupTrans();
        $this->migrateRoleTrans();
        $this->migrateFields();
        $this->migrateFieldPerms();
        $this->migrateFloatValues();
        $this->migrateStringValues();
        $this->migrateTextValues();
        $this->migrateListValues();
        $this->migrateRecords();
        $this->migrateReads();
        $this->migrateRecordSubscribes();
        $this->migrateEvents();
        $this->migrateFieldValues();
        $this->migrateChanges();
        $this->migrateComments();
        $this->migrateAttachments();

        // Process existing attachments.
        $this->processAttachments();

        // Drop all legacy tables.
        // ...
    }

    /**
     * {@inheritdoc}
     */
    public function down(Schema $schema)
    {
        $this->throwIrreversibleMigrationException();
    }

    /**
     * Migrates data from "tbl_accounts".
     */
    protected function migrateAccounts()
    {
        $sql = 'INSERT INTO users (id, provider, username, fullname, email, description, is_admin, is_disabled, password, settings) '
             . 'VALUES (:id, :provider, :username, :fullname, :email, :description, :is_admin, :is_disabled, :password, :settings);';

        $rows = $this->connection->fetchAll('SELECT * FROM tbl_accounts ORDER BY account_id');

        foreach ($rows as $row) {

            $locale   = Legacy\Locale::get($row['locale']);
            $timezone = Legacy\Timezone::get($row['timezone']);
            $theme    = strtolower($row['theme_name']);

            $this->addSql($sql, [
                'id'          => $row['account_id'],
                'provider'    => $row['is_ldapuser'] ? AuthenticationProvider::LDAP : AuthenticationProvider::ETRAXIS,
                'username'    => str_replace('@eTraxis', null, $row['username']),
                'fullname'    => $row['fullname'],
                'email'       => $row['email'],
                'description' => $row['description'],
                'is_admin'    => $row['is_admin'],
                'is_disabled' => $row['is_disabled'],
                'password'    => $row['passwd'],
                'settings'    => sprintf('{"locale":"%s","theme":"%s","timezone":"%s"}', $locale, $theme, $timezone),
            ]);
        }
    }

    /**
     * Migrates data from "tbl_projects".
     */
    protected function migrateProjects()
    {
        $sql = 'INSERT INTO projects (id, name, description, created_at, is_suspended) '
             . 'SELECT project_id, project_name, description, start_time, is_suspended '
             . 'FROM tbl_projects ORDER by project_id;';

        $this->addSql($sql);
    }

    /**
     * Migrates data from "tbl_groups".
     */
    protected function migrateGroups()
    {
        $sql = 'INSERT INTO groups (id, project_id, name, description) '
             . 'SELECT group_id, project_id, group_name, description '
             . 'FROM tbl_groups ORDER by group_id;';

        $this->addSql($sql);
    }

    /**
     * Migrates data from "tbl_membership".
     */
    protected function migrateMembership()
    {
        $sql = 'INSERT INTO membership (group_id, user_id) '
             . 'SELECT group_id, account_id '
             . 'FROM tbl_membership;';

        $this->addSql($sql);
    }

    /**
     * Migrates data from "tbl_templates".
     */
    protected function migrateTemplates()
    {
        $sql = 'INSERT INTO templates (id, project_id, name, prefix, critical_age, frozen_time, description, is_locked) '
             . 'SELECT template_id, project_id, template_name, template_prefix, critical_age, frozen_time, description, is_locked '
             . 'FROM tbl_templates ORDER by template_id;';

        $this->addSql($sql);

        $sql = 'INSERT INTO template_role_permissions (template_id, role, permission) '
             . 'VALUES (:template, :role, :permission);';

        $rows = $this->connection->fetchAll('SELECT * FROM tbl_templates ORDER BY template_id');

        foreach ($rows as $row) {

            foreach (Legacy\SystemRole::all() as $column => $role) {

                foreach (Legacy\TemplatePermission::all() as $old => $new) {

                    if (($row[$column] & $old) !== 0) {

                        $this->addSql($sql, [
                            'template'   => $row['template_id'],
                            'role'       => $role,
                            'permission' => $new,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Migrates data from "tbl_group_perms".
     */
    protected function migrateGroupPerms()
    {
        $sql = 'INSERT INTO template_group_permissions (template_id, group_id, permission) '
             . 'VALUES (:template, :group, :permission);';

        $rows = $this->connection->fetchAll('SELECT * FROM tbl_group_perms ORDER BY template_id, group_id');

        foreach ($rows as $row) {

            foreach (Legacy\TemplatePermission::all() as $old => $new) {

                if (($row['perms'] & $old) !== 0) {

                    $this->addSql($sql, [
                        'template'   => $row['template_id'],
                        'group'      => $row['group_id'],
                        'permission' => $new,
                    ]);
                }
            }
        }
    }

    /**
     * Migrates data from "tbl_states".
     */
    protected function migrateStates()
    {
        $sql = 'INSERT INTO states (id, template_id, name, abbreviation, type, responsible) '
             . 'SELECT state_id, template_id, state_name, SUBSTR(state_abbr FROM 1 FOR 5), state_type, responsible '
             . 'FROM tbl_states ORDER by state_id;';

        $this->addSql($sql);

        $rows = $this->connection->fetchAll('SELECT * FROM tbl_states ORDER BY state_id');

        foreach ($rows as $row) {
            $this->addSql('UPDATE states SET next_state_id = :next_state WHERE id = :id;', [
                'id'         => $row['state_id'],
                'next_state' => $row['next_state_id'],
            ]);
        }

        foreach (Legacy\StateType::all() as $old => $new) {
            $this->addSql('UPDATE states SET type = :new WHERE type = :old;', [
                'old' => $old,
                'new' => $new,
            ]);
        }

        foreach (Legacy\StateResponsible::all() as $old => $new) {
            $this->addSql('UPDATE states SET responsible = :new WHERE responsible = :old;', [
                'old' => $old,
                'new' => $new,
            ]);
        }
    }

    /**
     * Migrates data from "tbl_state_assignees".
     */
    protected function migrateStateAssignees()
    {
        $sql = 'INSERT INTO state_responsibles (state_id, group_id) '
             . 'SELECT state_id, group_id '
             . 'FROM tbl_state_assignees;';

        $this->addSql($sql);
    }

    /**
     * Migrates data from "tbl_group_trans".
     */
    protected function migrateGroupTrans()
    {
        $sql = 'INSERT INTO state_group_transitions (state_id_from, state_id_to, group_id) '
             . 'SELECT state_id_from, state_id_to, group_id '
             . 'FROM tbl_group_trans;';

        $this->addSql($sql);
    }

    /**
     * Migrates data from "tbl_role_trans".
     */
    protected function migrateRoleTrans()
    {
        $sql = 'INSERT INTO state_role_transitions (state_id_from, state_id_to, role) '
             . 'SELECT state_id_from, state_id_to, role '
             . 'FROM tbl_role_trans;';

        $this->addSql($sql);

        $this->addSql('UPDATE state_role_transitions SET role = :new WHERE role = :old;', ['old' => -1, 'new' => SystemRole::AUTHOR]);
        $this->addSql('UPDATE state_role_transitions SET role = :new WHERE role = :old;', ['old' => -2, 'new' => SystemRole::RESPONSIBLE]);
        $this->addSql('UPDATE state_role_transitions SET role = :new WHERE role = :old;', ['old' => -3, 'new' => SystemRole::ANYONE]);
    }

    /**
     * Migrates data from "tbl_fields".
     */
    protected function migrateFields()
    {
        $sql = 'INSERT INTO fields (id, state_id, name, type, description, field_order, removed_at, is_required, pcre_check, pcre_search, pcre_replace, parameter1, parameter2, default_value) '
             . 'SELECT field_id, state_id, field_name, field_type, description, field_order, removal_time, is_required, regex_check, regex_search, regex_replace, param1, param2, value_id '
             . 'FROM tbl_fields ORDER by field_id;';

        $this->addSql($sql);

        $this->addSql('UPDATE fields SET removed_at = NULL WHERE removed_at = 0;');

        foreach (Legacy\FieldType::all() as $old => $new) {
            $this->addSql('UPDATE fields SET type = :new WHERE type = :old;', [
                'old' => $old,
                'new' => $new,
            ]);
        }

        $sql = 'INSERT INTO field_role_permissions (field_id, role, permission) '
             . 'VALUES (:field, :role, :permission);';

        $rows = $this->connection->fetchAll('SELECT * FROM tbl_fields ORDER BY field_id');

        foreach ($rows as $row) {

            foreach (Legacy\SystemRole::all() as $column => $role) {

                if ((int) $row[$column] !== 0) {
                    $this->addSql($sql, [
                        'field'      => $row['field_id'],
                        'role'       => $role,
                        'permission' => FieldPermission::READ_ONLY,
                    ]);
                }

                if ((int) $row[$column] === 2) {
                    $this->addSql($sql, [
                        'field'      => $row['field_id'],
                        'role'       => $role,
                        'permission' => FieldPermission::READ_WRITE,
                    ]);
                }
            }
        }
    }

    /**
     * Migrates data from "tbl_field_perms".
     */
    protected function migrateFieldPerms()
    {
        $sql = 'INSERT INTO field_group_permissions (field_id, group_id, permission) '
             . 'SELECT field_id, group_id, perms '
             . 'FROM tbl_field_perms;';

        $this->addSql($sql);

        $this->addSql('UPDATE field_group_permissions SET permission = :new WHERE permission = :old;', [
            'old' => 1,
            'new' => FieldPermission::READ_ONLY,
        ]);

        $this->addSql('UPDATE field_group_permissions SET permission = :new WHERE permission = :old;', [
            'old' => 2,
            'new' => FieldPermission::READ_WRITE,
        ]);
    }

    /**
     * Migrates data from "tbl_float_values".
     */
    protected function migrateFloatValues()
    {
        $sql = 'INSERT INTO decimal_values (id, value) '
             . 'SELECT value_id, float_value '
             . 'FROM tbl_float_values;';

        $this->addSql($sql);
    }

    /**
     * Migrates data from "tbl_string_values".
     */
    protected function migrateStringValues()
    {
        $sql = 'INSERT INTO string_values (id, token, value) '
             . 'SELECT value_id, value_token, string_value '
             . 'FROM tbl_string_values;';

        $this->addSql($sql);
    }

    /**
     * Migrates data from "tbl_text_values".
     */
    protected function migrateTextValues()
    {
        $sql = 'INSERT INTO text_values (id, token, value) '
             . 'SELECT value_id, value_token, text_value '
             . 'FROM tbl_text_values;';

        $this->addSql($sql);
    }

    /**
     * Migrates data from "tbl_list_values".
     */
    protected function migrateListValues()
    {
        $sql = 'INSERT INTO list_items (field_id, item_value, item_text) '
             . 'SELECT field_id, int_value, str_value '
             . 'FROM tbl_list_values;';

        $this->addSql($sql);
    }

    /**
     * Migrates data from "tbl_records".
     */
    protected function migrateRecords()
    {
        $sql = 'INSERT INTO records (id, state_id, author_id, responsible_id, subject, created_at, changed_at, closed_at, resumed_at) '
             . 'SELECT record_id, state_id, creator_id, responsible_id, subject, creation_time, change_time, closure_time, postpone_time '
             . 'FROM tbl_records;';

        $this->addSql($sql);
    }

    /**
     * Migrates data from "tbl_reads".
     */
    protected function migrateReads()
    {
        $sql = 'INSERT INTO last_reads (record_id, user_id, read_at) '
             . 'SELECT record_id, account_id, read_time '
             . 'FROM tbl_reads;';

        $this->addSql($sql);
    }

    /**
     * Migrates data from "tbl_record_subscribes".
     */
    protected function migrateRecordSubscribes()
    {
        $sql = 'INSERT INTO watchers (record_id, watcher_id, initiator_id) '
             . 'SELECT record_id, account_id, subscribed_by '
             . 'FROM tbl_record_subscribes;';

        $this->addSql($sql);
    }

    /**
     * Migrates data from "tbl_events".
     */
    protected function migrateEvents()
    {
        $sql = 'INSERT INTO events (id, record_id, user_id, type, created_at, parameter) '
             . 'SELECT event_id, record_id, originator_id, event_type, event_time, event_param '
             . 'FROM tbl_events '
             . 'WHERE event_type < 100;';

        $this->addSql($sql);

        foreach (Legacy\EventType::all() as $old => $new) {
            $this->addSql('UPDATE events SET type = :new WHERE type = :old;', [
                'old' => $old,
                'new' => $new,
            ]);
        }
    }

    /**
     * Migrates data from "tbl_field_values".
     */
    protected function migrateFieldValues()
    {
        $sql = 'INSERT INTO field_values (event_id, field_id, is_current, value) '
             . 'SELECT event_id, field_id, is_latest, value_id '
             . 'FROM tbl_field_values;';

        $this->addSql($sql);
    }

    /**
     * Migrates data from "tbl_changes".
     */
    protected function migrateChanges()
    {
        $sql = 'INSERT INTO changes (id, event_id, field_id, old_value, new_value) '
             . 'SELECT change_id, event_id, field_id, old_value_id, new_value_id '
             . 'FROM tbl_changes;';

        $this->addSql($sql);
    }

    /**
     * Migrates data from "tbl_comments".
     */
    protected function migrateComments()
    {
        $sql = 'INSERT INTO comments (id, event_id, comment_text, is_private) '
             . 'SELECT comment_id, event_id, comment_body, is_confidential '
             . 'FROM tbl_comments;';

        $this->addSql($sql);
    }

    /**
     * Migrates data from "tbl_attachments".
     */
    protected function migrateAttachments()
    {
        $sql = 'INSERT INTO attachments (id, event_id, file_name, file_size, mime_type, is_deleted) '
             . 'SELECT attachment_id, event_id, attachment_name, attachment_size, attachment_type, is_removed '
             . 'FROM tbl_attachments;';

        $this->addSql($sql);
    }

    /**
     * Process existing attachments.
     */
    protected function processAttachments()
    {
        $path = realpath(getcwd() . '/web/' . $this->container->getParameter('files_path'));

        foreach (scandir($path) as $entry) {

            if (!is_numeric($entry)) {
                continue;
            }

            $filename = $path . '/' . $entry;

            rename($filename, $filename . '.gz');

            $source = gzopen($filename . '.gz', 'rb');
            $dest   = fopen($filename, 'w');

            while (!gzeof($source)) {
                fwrite($dest, gzread($source, 1048576));
            }

            fclose($dest);
            gzclose($source);

            unlink($filename . '.gz');
        }
    }
}
