import 'package:eshopplus_deliveryboy/features/auth/repositories/availabilityRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

abstract class AvailabilityToggleState {}

class AvailabilityToggleInitial extends AvailabilityToggleState {}

class AvailabilityToggleInProgress extends AvailabilityToggleState {}

class AvailabilityToggleSuccess extends AvailabilityToggleState {
  final String message;
  final bool isAvailable;

  AvailabilityToggleSuccess({required this.message, required this.isAvailable});
}

class AvailabilityToggleFailure extends AvailabilityToggleState {
  final String errorMessage;

  AvailabilityToggleFailure({required this.errorMessage});
}

class AvailabilityToggleCubit extends Cubit<AvailabilityToggleState> {
  final AvailabilityRepository availabilityRepository;

  AvailabilityToggleCubit(this.availabilityRepository) : super(AvailabilityToggleInitial());

  void toggleAvailability({required bool isAvailable}) async {
    emit(AvailabilityToggleInProgress());
    try {
      final message = await availabilityRepository.toggleAvailability(
        isAvailable: isAvailable ? 1 : 0,
      );
      emit(AvailabilityToggleSuccess(message: message, isAvailable: isAvailable));
    } catch (e) {
      emit(AvailabilityToggleFailure(errorMessage: e.toString()));
    }
  }
}