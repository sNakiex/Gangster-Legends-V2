<?php

	class Cron {

		public function __construct($cronName, $cronType, $interval, $maxRepetitions = 1000) {
			global $db, $user;
			$this->db = $db;
			$this->user = $user;
			$this->settings = new Settings();
			$this->cronName = $cronName;
			$this->cronType = $cronType;
			$this->maxRepetitions = $maxRepetitions;
			$this->interval = $interval;
		}

		public function updateLastRun() {
			$key = "cron-" . $this->cronName;
			$newTime = time() - (time() % $this->interval);

			if ($this->cronType == "user" && $this->user) {
				$time = $this->user->updateTimer($key, $newTime);
			} else if ($this->cronType == "system") {
				$time = $this->settings->update($key, $newTime);
			}
			
			return $this;
		}

		public function getRepetitionCount () {
			$key = "cron-" . $this->cronName;
				
			if ($this->cronType == "user" && $this->user) {
				$time = $this->user->getTimer($key, false);
			} else if ($this->cronType == "system") {
				$time = $this->settings->loadSetting($key);
			}

			$count =  floor((time() - $time) / $this->interval);

			return $count > $this->maxRepetitions?$this->maxRepetitions:$count;

		}

		public function getCronDates() {

			$key = "cron-" . $this->cronName;
				
			if ($this->cronType == "user" && $this->user) {
				$time = $user->getTimer($key);
			} else if ($this->cronType == "system") {
				$time = $this->settings->loadSetting($key);
			}

			$count =  floor((time() - intval($time)) / $this->interval);
			if ($count > $this->maxRepetitions) {
				$count = $this->maxRepetitions;
			}

			if (!$count) return array();

			$start = time() - time() % $this->interval;

			$dates = array();

			while ($count) {
				$dates[] = $start - ($this->interval * ($count - 1));
				$count--;
			}

			return $dates;
		}

	}