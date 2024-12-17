<?php

namespace Tests\Unit;

use App\Models\Resource;
use App\Models\Reservation;
use App\Services\ReservationService;
use App\Services\ResourceService;
use App\Exceptions\ResourceUnavailableException;
use App\Exceptions\ReservationAlreadyCancelledException;
use App\Exceptions\ReservationAlreadyConfirmedException;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_prevents_creation_of_reservation_if_resource_is_unavailable() // Reservar recurso en horario ya reservado
    {

        $resource = Resource::create([
            'name' => 'Sala de reuniones',
            'description' => 'Una sala de reuniones para 10 personas',
            'capacity' => 10
        ]);

        $existingReservation = Reservation::create([
            'resource_id' => $resource->id,
            'reserved_at' => '2024-12-19 14:00:00',
            'duration' => '01:00:00',
            'status' => 'confirmed',
        ]);

        $now = Carbon::now();


        if (!$now->isWeekday()) {
            $reservedAt = $now->next(Carbon::MONDAY)->setTime(10, 0, 0);
        } elseif ($now->format('H:i') < '09:00') {
            $reservedAt = $now->setTime(10, 0, 0);
        } elseif ($now->format('H:i') > '18:00') {
            $reservedAt = $now->addDay()->setTime(10, 0, 0);
        } else {
            $reservedAt = $now->addHours(1);
        }

        $this->assertTrue(
            $reservedAt->isWeekday() &&
                $reservedAt->hour >= 9 && $reservedAt->hour < 18,
            'La reserva debe ser en un día hábil entre las 9 AM y las 6 PM'
        );

        $data = [
            'resource_id' => $resource->id,
            'reserved_at' => '2024-12-19 14:00:00',
            'duration' => '01:00:00',
        ];

        $this->expectException(ResourceUnavailableException::class);

        $service = app(ReservationService::class);
        $service->createReservation($data);
    }

    /** @test */
    public function it_creates_a_reservation_when_resource_is_available() // Reserva con recurso disponible en el horario
    {

        $resource = Resource::create([
            'name' => 'Sala de reuniones',
            'description' => 'Una sala de reuniones para 10 personas',
            'capacity' => 10
        ]);


        $now = Carbon::now();

        if ($now->format('H:i') < '09:00') {
            $reservedAt = $now->setTime(9, 0, 0);
        } elseif ($now->format('H:i') > '18:00') {
            $reservedAt = $now->addDay()->setTime(9, 0, 0);
        } else {
            $reservedAt = $now->addHours(1);
        }

        $data = [
            'resource_id' => $resource->id,
            'reserved_at' => $reservedAt,
            'duration' => '01:00:00',
        ];

        $service = app(ReservationService::class);
        $reservation = $service->createReservation($data);

        $this->assertDatabaseHas('reservations', [
            'resource_id' => $resource->id,
            'reserved_at' => $reservedAt->toDateTimeString(),
            'duration' => $data['duration'],
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(Reservation::class, $reservation);
    }


    /** @test */
    public function it_can_cancel_a_reservation() // Cancelar una reservacion
    {
        $resource = Resource::create([
            'name' => 'Sala de reuniones',
            'description' => 'Una sala de reuniones para 10 personas',
            'capacity' => 10
        ]);

        $reservation = Reservation::create([
            'resource_id' => $resource->id,
            'reserved_at' => '2024-12-19 14:00:00',
            'duration' => '01:00:00',
            'status' => 'confirmed',
        ]);

        $service = app(ReservationService::class);
        $response = $service->cancelReservation($reservation->id);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'cancelled',
        ]);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('success', $response);
        $this->assertEquals('Reservation successfully canceled.', $response['message']);
        $this->assertTrue($response['success']);
    }

    /** @test */
    public function it_prevents_canceling_a_reservation_that_is_already_cancelled() // Cancelar una reserva que ya está cancelada
    {
        $resource = Resource::create([
            'name' => 'Sala de reuniones',
            'description' => 'Una sala de reuniones para 10 personas',
            'capacity' => 10
        ]);

        $reservation = Reservation::create([
            'resource_id' => $resource->id,
            'reserved_at' => '2024-12-19 14:00:00',
            'duration' => '01:00:00',
            'status' => 'confirmed',
        ]);

        $service = app(ReservationService::class);
        $service->cancelReservation($reservation->id);

        $this->expectException(ReservationAlreadyCancelledException::class);

        $service->cancelReservation($reservation->id);
    }
}
