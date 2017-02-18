<?php
use Ifsnop\Mysqldump as IMysqldump;
use Respect\Validation\Validator as DataValidator;

class DownloadController extends Controller {
    const PATH = '/download';
    const METHOD = 'GET';

    public function validations() {
        return [
            'permission' => 'any',
            'requestData' => [
                'file' => [
                    'validation' => DataValidator::alnum('_.-')->noWhitespace(),
                    'error' => ERRORS::INVALID_FILE
                ]
            ]
        ];
    }

    public function handler() {
        $fileName = Controller::request('file');
        $staffUser = Staff::getDataStore($fileName, 'profilePic');

        if($staffUser->isNull()) {
            $loggedUser = Controller::getLoggedUser();

            if($loggedUser->isNull()) {
                print '';
                return;
            }

            $ticket = Ticket::getTicket($fileName, 'file');

            if($ticket->isNull() || ($this->isNotAuthor($ticket, $loggedUser) && $this->isNotOwner($ticket, $loggedUser))) {
                $ticketEvent = Ticketevent::getDataStore($fileName, 'file');

                if($ticketEvent->isNull()) {
                    print '';
                    return;
                }

                $ticket = $ticketEvent->ticket;

                if($this->isNotAuthor($ticket, $loggedUser) && $this->isNotOwner($ticket, $loggedUser)) {
                    print '';
                    return;
                }
            }
        }

        $fileDownloader = FileDownloader::getInstance();
        $fileDownloader->setFileName($fileName);
        $fileDownloader->download();
    }

    private function isNotAuthor($ticket, $loggedUser) {
        return Controller::isStaffLogged() || $ticket->author->id !== $loggedUser->id;
    }

    private function isNotOwner($ticket, $loggedUser) {
        return !Controller::isStaffLogged() || !$ticket->owner || $ticket->owner->id !== $loggedUser->id;
    }
}