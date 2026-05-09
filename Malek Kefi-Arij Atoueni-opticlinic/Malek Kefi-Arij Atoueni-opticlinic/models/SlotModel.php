<?php
class SlotModel
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Release a time slot by setting it as available
     * @param PDO $pdo
     * @param int $slotId
     */
    public static function releaseSlot($pdo, $slotId)
    {
        // Mark slot as available
        $stmt = $pdo->prepare("UPDATE time_slots SET is_available = 1 WHERE id = ?"); // Update slot availability to true
        $stmt->execute([$slotId]);
    }

    /**
     * Reserve a time slot by setting it as unavailable
     * @param PDO $pdo
     * @param int $slotId
     */
    public static function reserveSlot($pdo, $slotId)
    {
        // Mark slot as unavailable
        $stmt = $pdo->prepare("UPDATE time_slots SET is_available = 0 WHERE id = ?"); // Update slot availability to false
        $stmt->execute([$slotId]);
    }

    /**
     * Get available time slots with optional limit
     * @param PDO $pdo
     * @param int|null $limit
     * @return array
     */
    public static function getAvailableSlots($pdo, $limit = null)
    {
        // Retrieve available slots, optionally limited
        if ($limit !== null) {
            $limit = (int)$limit;
            $stmt = $pdo->prepare("SELECT * FROM time_slots WHERE is_available = 1 ORDER BY date, start_time LIMIT :limit"); // Select available slots with limit
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $stmt = $pdo->query("SELECT * FROM time_slots WHERE is_available = 1 ORDER BY date, start_time"); // Select all available slots
        }
        return $stmt->fetchAll();
    }

    /**
     * Get time slots for a specific date
     * @param PDO $pdo
     * @param string $date
     * @return array
     */
    public static function getSlotsByDate($pdo, $date)
    {
        // Retrieve slots for the given date
        $stmt = $pdo->prepare("SELECT id, date, start_time, end_time, is_available FROM time_slots WHERE date = ? ORDER BY start_time"); // Select slots by date
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all future time slots
     * @param PDO $pdo
     * @return array
     */
    public static function getFutureSlots($pdo)
    {
        // Retrieve slots from today onwards
        $stmt = $pdo->prepare("SELECT id, date, start_time, end_time, is_available FROM time_slots WHERE date >= CURDATE() ORDER BY date, start_time"); // Select future slots
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
